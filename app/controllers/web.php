<?php

namespace Fir\Controllers;

use Fir\Libraries\HexConverter;
use Fir\Libraries\MorseCode;
use Fir\Libraries\LoremIpsum;
use Fir\Libraries\Search;

/**
 * Controller responsável pela busca web (sites)
 * Processa requisições de busca, aplica filtros, paginação, respostas instantâneas e exibe resultados web
 * 
 * Funcionalidades principais:
 * - Busca de sites na web
 * - Controle de limite de buscas por IP
 * - Filtros de busca (período, tipo de arquivo, safe search)
 * - Paginação de resultados
 * - Respostas instantâneas (calculadora, conversões, etc.)
 * - Formatação de dados (datas, visualizações, duração)
 * - Suporte a múltiplos mercados/idiomas
 */
class Web extends Controller {

    /**
     * @var object
     */
    protected $model;

    /**
     * Método principal que processa as buscas web.
     * 
     * Fluxo de execução:
     * 1. Verifica limite de buscas por IP
     * 2. Valida parâmetros de busca
     * 3. Configura filtros de resposta da API
     * 4. Aplica filtros de busca (período, tipo, safe search)
     * 5. Executa a busca na API
     * 6. Processa e formata os resultados
     * 7. Renderiza a view com os dados
     * 
     * @return array Dados processados para renderização
     */
    public function index() {
        $search_limit = false;

        $data['menu_view'] = $this->getMenu();

        // Se o limite de busca por IP está habilitado
        if($this->settings['search_per_ip'] > 0) {
            $this->model = $this->model('SearchLimit');

            $user = $this->model->getIp(['ip' => $_SERVER['REMOTE_ADDR']]);

            $user['count'] = isset($user['count']) ? $user['count'] : 0;

            // Se o usuário excedeu o limite de consultas permitidas no período
            if($user['count'] >= $this->settings['search_per_ip'] && (time()-strtotime($user['updated_at']) < $this->settings['search_time'])) {
                $search_limit = true;
            } else {
                // Reseta o contador se o período de tempo foi excedido
                if(isset($user['updated_at']) && time()-strtotime($user['updated_at']) > $this->settings['search_time']) {
                    $this->model->resetIp(['ip' => $_SERVER['REMOTE_ADDR']]);
                } else {
                    $this->model->addIp(['ip' => $_SERVER['REMOTE_ADDR'], 'count' => $user['count']+1]);
                }
            }
        }

        // Se não há consulta, redireciona para a página inicial
        if(isset($_GET['q']) == false || empty($_GET['q']) || $this->settings['web_per_page'] == 0) {
            redirect();
        }

        $search = new Search();
        $search->key = $this->settings['search_api_key'];
        $search->endpoint = 'search';

        $responseFilter[] = "WebPages";
        // Inclui o tipo de busca no responseFilter, se estiverem habilitados
        if($this->settings['images_per_page'] > 0 && $this->settings['search_answers']) {
            $responseFilter[] = "Images";
        }
        if($this->settings['videos_per_page'] > 0 && $this->settings['search_answers']) {
            $responseFilter[] = "Videos";
        }
        if($this->settings['news_per_page'] > 0 && $this->settings['search_answers']) {
            $responseFilter[] = "News";
        }
        if($this->settings['search_related'] > 0) {
            $responseFilter[] = "RelatedSearches";
        }
        if($this->settings['search_entities']) {
            // Solicita entidades apenas dos mercados disponíveis
            if(in_array((isset($_COOKIE['market']) && in_array($_COOKIE['market'], array_keys($search->getMarkets())) ? $_COOKIE['market'] : $this->settings['search_market']), ['en-AU', 'en-CA', 'fr-CA', 'fr-FR', 'de-DE', 'en-IN', 'it-IT', 'es-MX', 'en-GB', 'en-US', 'en-US', 'es-US', 'es-ES', 'pt-BR'])) {
                $responseFilter[] = 'Entities';
            }
        }
        $search->responseFilter = implode(',', $responseFilter);

        $perPage = $this->settings['web_per_page'];
        $filters = $this->searchFilters(true);

        // Filtro de resultados por página (adiciona +1 resultado para verificar se há resultados extras para próxima página
        $params['count'] = $perPage+1;
        // Filtro de paginação
        if(isset($_GET['offset']) && ctype_digit($_GET['offset'])) {
            $params['offset'] = $_GET['offset'];
        } else {
            $params['offset'] = 0;
        }
        // Filtro de período passado
        if(isset($_GET['freshness']) && in_array($_GET['freshness'], array_keys($filters[0]['period'][1]))) {
            $params['freshness'] = $_GET['freshness'];
        }
        // Filtro de tipo de arquivo
        if(isset($_GET['fileType']) && in_array($_GET['fileType'], array_keys($filters[0]['type'][1]))) {
            $fileType = 'filetype:'.$_GET['fileType'].' ';
        } else {
            $fileType = '';
        }
        // Filtro de busca segura
        if(isset($_GET['safeSearch']) && in_array($_GET['safeSearch'], array_keys($filters[0]['safe_search'][1]))) {
            $params['safeSearch'] = $_GET['safeSearch'];
        } else {
            $params['safeSearch'] = $_COOKIE['safe_search'];
        }
        // Filtro de destaque
        $params['textDecorations'] = $_COOKIE['highlight'];
        $params['textFormat'] = 'HTML';

        // Filtro de filtros
        if(isset($_GET['filters'])) {
            $params['filters'] = $_GET['filters'];
        }

        // Mercado
        $params['mkt'] = (in_array($_COOKIE['market'], array_keys($search->getMarkets())) ? $_COOKIE['market'] : 'en-US');

        // Busca em sites específicos
        $specificSites = $search->specificSites($this->settings['search_sites']);

        // Consulta
        $params['q'] = $fileType.$_GET['q'].$specificSites;

        // Obtém Respostas Instantâneas se estiver na primeira página da busca
        $data['result_ia_view'] = ($params['offset'] == 0 ? $this->evaluateQuery($_GET['q']) : false);

        if($search_limit == false) {
            $request = $search->request($params);
        } else {
            $request = false;
        }

        $data['filters_view'] = $this->searchFilters();
		
		if(isset($_GET['debug']) && $_GET['debug']) {
			var_dump(substr($request, 0, 1024));
		}

        $data['response'] = json_decode($request, true);

        // Se o recurso Safe Ads está habilitado e o Safe Search está desligado
        if($this->settings['ads_safe'] == 1 && $params['safeSearch'] == 'Off') {
            $data['show_ads'] = false;
        } else {
            $data['show_ads'] = true;
        }
        $data['entities_view'] = false;

        $errType = 0;

        if(isset($data['response']['webPages']['value']) && !empty($data['response']['webPages']['value']) && $search_limit == false) {
            // Valida dados
            if(isset($data['response']['images']['value'])) {
                foreach($data['response']['images']['value'] as $key => $value) {
                    // Obtém o nome do domínio da URL e remove o prefixo www
                    $data['response']['images']['value'][$key]['displayUrl'] = str_replace('www.', '', parse_url($value['hostPageUrl'], PHP_URL_HOST));
                }
            }

            if(isset($data['response']['videos']['value'])) {
                foreach($data['response']['videos']['value'] as $key => $value) {
                    // Formata o contador de visualizações
                    $data['response']['videos']['value'][$key]['viewCount'] = isset($value['viewCount']) ? formatViews($value['viewCount']) : false;
                    // Formata a data de publicação
                    $date = isset($value['datePublished']) ? explode('-', date('Y-m-d', strtotime($value['datePublished']))) : '';
                    $data['response']['videos']['value'][$key]['datePublished'] = !empty($date) ? sprintf($this->lang['date_format'], $date[0], substr($this->lang['month_'.$date[1]], 0, 3), $date[2]) : '';

                    if(isset($value['duration'])) {
                        $formatTime = new \DateInterval($value['duration']);
                        $data['response']['videos']['value'][$key]['duration'] = formatDuration($formatTime->format('%H:%I:%S'));
                    }
                }
            }

            if(isset($data['response']['news']['value'])) {
                foreach($data['response']['news']['value'] as $key => $value) {
                    // Formata a data de publicação
                    $date = isset($value['datePublished']) ? explode('-', date('Y-m-d', strtotime($value['datePublished']))) : '';
                    $data['response']['news']['value'][$key]['datePublished'] = !empty($date) ? sprintf($this->lang['date_format'], $date[0], substr($this->lang['month_'.$date[1]], 0, 3), $date[2]) : '';
                }
            }

            // Se há resultados extras para a próxima página
            foreach($data['response']['rankingResponse']['mainline']['items'] as $key => $value) {
                if ($key >= $perPage) {
                    $data['next_button'] = true;
                    unset($data['response']['rankingResponse']['mainline']['items'][$key]);
                } else {
                    $data['next_button'] = false;
                }
            }

            $data['estimated_results'] = $data['response']['webPages']['totalEstimatedMatches'];
            $data['query'] = $_GET['q'];
            $data['filters'] = $filters[1];
            $data['prev_button'] = $params['offset'] > 0 ? true : false;
            $data['current_page'] = $params['offset'];
            $data['per_page'] = $perPage;

            if(isset($data['response']['entities'])) {
                foreach($data['response']['entities']['value'] as $key => $value) {
                    // Formata a URL de exibição
                    if(isset($value['url'])) {
                        $data['response']['entities']['value'][$key]['displayUrl'] = str_replace('www.', '', parse_url($value['url'], PHP_URL_HOST));
                    }

                    if(isset($value['contractualRules'])) {
                        foreach($value['contractualRules'] as $y => $z) {
                            // Check for any contractual rule required, and store it as a flag
                            if($z['_type'] == 'ContractualRules/MediaAttribution') {
                                $data['response']['entities']['value'][$key]['helper']['contract']['media']['attribution'] = true;
                            }

                            if(($z['_type'] == 'ContractualRules/TextAttribution' || $z['_type'] == 'ContractualRules/LinkAttribution') && $z['targetPropertyName'] == 'description') {
                                $data['response']['entities']['value'][$key]['helper']['contract']['description']['attribution'] = true;
                            }

                            if($z['_type'] == 'ContractualRules/LicenseAttribution' && $z['targetPropertyName'] == 'description') {
                                $data['response']['entities']['value'][$key]['helper']['contract']['description']['license'] = true;
                            }

                            if(($z['_type'] == 'ContractualRules/TextAttribution' || $z['_type'] == 'ContractualRules/LinkAttribution') && isset($z['targetPropertyName']) == false) {
                                $data['response']['entities']['value'][$key]['helper']['contract']['footer']['attribution'] = true;
                            }
                        }
                    }
                }

                if(isset($entityDominant)) {
                    $data['entity_dominant'] = array_slice($data['response']['entities']['value'], 0, 1);
                }

                $data['entities_view'] = $this->view->render($data, 'web/entities');
            }

            $data['pagination_view'] = $this->view->render($data, 'shared/search_pagination');
            $data['search_results_view'] = $this->view->render($data, 'web/search_results');
        } else {
            // If the API returns an error and there's no previous error (prevents looping)
            if(((isset($data['response']['statusCode']) && isset($data['response']['message']) && isset($_SESSION['message']) == false) && $errType = 1) || (isset($data['response']['errors']) && isset($_SESSION['message']) == false && $errType = 2) || ($search_limit == true && isset($_SESSION['message']) == false)) {
                if($search_limit) {
                    $_SESSION['message'][] = ['info', $this->lang['search_l_e']];
                } elseif($errType == 1) {
                    $_SESSION['message'][] = ['error', $data['response']['statusCode'].$data['response']['message']];
                } elseif($errType == 2) {
                    foreach($data['response']['errors'] as $error) {
                        $_SESSION['message'][] = ['error', $error['moreDetails'].$error['parameter'].$error['value']];
                    }
                }
                redirect('web?q='.urlencode($_GET['q']));
            }

            $data['search_results_view'] = $this->view->render($data, 'shared/search_error');
        }

        $this->view->metadata['title'] = [$this->lang['web'], $_GET['q']];

        return ['content' => $this->view->render($data, 'web/content')];
    }

    /**
     * Avalia a consulta para fornecer uma Resposta Instantânea.
     * 
     * Este método analisa a consulta do usuário e identifica padrões específicos
     * que podem ser respondidos instantaneamente sem precisar fazer uma busca na web.
     * 
     * Funcionalidades suportadas:
     * - Informações do usuário (IP, hora, data)
     * - Ferramentas (moeda, cronômetro, dados)
     * - Conversões (QR Code, cores hex, MD5, Base64)
     * - Manipulação de texto (ordenar, inverter, case)
     * - Cálculos (ano bissexto, PI, tempo Unix)
     * - Criptografia (Morse, Atbash, UUID)
     * 
     * @param   string  $query A string a ser avaliada
     * @return  string  HTML da resposta instantânea ou false se não houver match
     */
    private function evaluateQuery($query) {
        // Qual é meu IP
        foreach($this->lang['ia']['ip'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaGetUserIp();
            }
        }

        // Hora atual do usuário
        foreach($this->lang['ia']['time'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaGetUserTime();
            }
        }

        // Data atual do usuário
        foreach($this->lang['ia']['date'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaGetUserDate();
            }
        }

        // Jogar moeda
        foreach($this->lang['ia']['flip_coin'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaFlipCoin();
            }
        }

        // Cronômetro
        foreach($this->lang['ia']['stopwatch'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaStopwatch();
            }
        }

        // Jogar dados
        foreach($this->lang['ia']['roll'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de dígitos
            if(preg_match(sprintf('/%s ([0-9]+)/iu', $t), $query, $match)) {
                return $this->iaRoll($match);
            }
        }

        // Código QR
        foreach($this->lang['ia']['qr_code'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string ou string seguida do trigger
            if(preg_match(sprintf('/^%s\s(.+)|(.+)\s%s$/iu', $t, $t), $query, $match)) {
                return $this->iaQrCode($match);
            }
        }

        // Ordenar decrescente
        foreach($this->lang['ia']['sort_desc'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaSort($match, 2);
            }
        }

        // Ordenar crescente
        foreach($this->lang['ia']['sort_asc'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaSort($match, 1);
            }
        }

        // Inverter texto
        foreach($this->lang['ia']['reverse_text'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaReverseText($match);
            }
        }

        // Cor hexadecimal
        if(preg_match('/^#([a-fA-F0-9]{6})$/iu', $query, $match) || preg_match('/^#([a-fA-F0-9]{3})/iu', $query, $match)) {
            return $this->iaHexColor($match);
        }

        // MD5
        foreach($this->lang['ia']['md5'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaMD5($match);
            }
        }

        // Base64 codificar
        foreach($this->lang['ia']['base64_encode'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaBase64($match, 1);
            }
        }

        // Base64 decodificar
        foreach($this->lang['ia']['base64_decode'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaBase64($match, 2);
            }
        }

        // Minúsculas
        foreach($this->lang['ia']['lowercase'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaCase($match, 1);
            }
        }

        // Maiúsculas
        foreach($this->lang['ia']['uppercase'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaCase($match, 2);
            }
        }

        // Camelcase
        foreach($this->lang['ia']['camelcase'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaCase($match, 3);
            }
        }

        // Ano bissexto
        foreach($this->lang['ia']['leap_year'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de dígitos ou dígitos seguidos do trigger
            if(preg_match(sprintf('/(.*?)(%s)(.*?)(\d+)(.*?)$/iu', $t), $query, $match) || preg_match(sprintf('/(.*?)(\d+)(.*?)(%s)(.*?)$/iu', $t), $query, $match)) {
                return $this->iaLeapYear($match);
            }
        }

        // Resolução da tela
        foreach($this->lang['ia']['screen_resolution'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaUserScreenResolution();
            }
        }

        // Pi
        foreach($this->lang['ia']['pi'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaPi();
            }
        }

        // Código Morse
        foreach($this->lang['ia']['morse_code'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de código morse
            if(preg_match(sprintf('/^%s\s([.\-\/\s]+)*$/iu', $t), $query, $match)) {
                return $this->iaMorseCode($match, 2);
            }

            // Verifica se a consulta corresponde ao trigger seguido de string
            if(preg_match(sprintf('/^%s\s(.+)$/iu', $t), $query, $match)) {
                return $this->iaMorseCode($match, 1);
            }
        }

        // Tempo Unix
        foreach($this->lang['ia']['unix_time'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de dígitos
            if(preg_match(sprintf('/^%s\s(\d+)$/iu', $t), $query, $match)) {
                return $this->iaUnixTime($match, 1);
            }

            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/iu', $t), $query, $match)) {
                return $this->iaUnixTime($match, 2);
            }
        }

        // Lorem Ipsum
        foreach($this->lang['ia']['lorem_ipsum'] as $t) {
            // Verifica se a consulta corresponde ao trigger ou trigger seguido de dígitos
            if(preg_match(sprintf('/^(%s)\s?(\d+)?$/ui', $t), $query, $match)) {
                return $this->iaLoremIpsum($match);
            }
        }

        // Cifra Atbash
        foreach($this->lang['ia']['atbash'] as $t) {
            // Verifica se a consulta corresponde ao trigger seguido de string alfanumérica
            if(preg_match(sprintf('/^%s\s([a-z0-9\s]+)$/i', $t), $query, $match)) {
                return $this->iaAtbashCipher($match);
            }
        }

        // UUID
        foreach($this->lang['ia']['uuid'] as $t) {
            // Verifica se a consulta corresponde ao trigger
            if(preg_match(sprintf('/^%s$/ui', $t), $query, $match)) {
                return $this->iaUUID($match);
            }
        }

        return false;
    }

    /**
     * Retorna o endereço IP do usuário.
     * 
     * @return string HTML renderizado com o IP do usuário
     */
    private function iaGetUserIp() {
        $data['result'] = $_SERVER['REMOTE_ADDR'];
        return $this->view->render($data, 'web/ia/user_ip');
    }

    /**
     * Retorna a hora atual do usuário.
     * Prepara dados dos meses para formatação da hora.
     * 
     * @return string HTML renderizado com a hora atual
     */
    private function iaGetUserTime() {
        $data = [];
        for($i = 1; $i <= 12; $i++) {
            $data['months'][] = $this->lang['month_'.sprintf('%02d', $i)];
        }
        return $this->view->render($data, 'web/ia/user_time');
    }

    /**
     * Retorna a data atual do usuário.
     * Prepara dados dos meses para formatação da data.
     * 
     * @return string HTML renderizado com a data atual
     */
    private function iaGetUserDate() {
        $data = [];
        for($i = 1; $i <= 12; $i++) {
            $data['months'][] = $this->lang['month_'.sprintf('%02d', $i)];
        }
        return $this->view->render($data, 'web/ia/user_date');
    }

    /**
     * Simula o lançamento de uma moeda (cara ou coroa).
     * Gera um número aleatório entre 0 e 1 para determinar o resultado.
     * 
     * @return string HTML renderizado com o resultado do lançamento
     */
    private function iaFlipCoin() {
        $rand = rand(0, 1);
        $data['result'] = $this->lang['coin_'.$rand];
        return $this->view->render($data, 'web/ia/flip_coin');
    }

    /**
     * Exibe um cronômetro interativo.
     * 
     * @return string HTML renderizado com o cronômetro
     */
    private function iaStopwatch() {
        return $this->view->render(null, 'web/ia/stopwatch');
    }

    /**
     * Simula o lançamento de dados com um número específico de faces.
     * 
     * Validações:
     * - Se o valor for maior que 1 milhão, define como 1 milhão (evita quebrar o valor máximo do mt_rand)
     * - Se o valor for menor que 1, define como 1
     * 
     * @param array $value Array contendo o número de faces do dado
     * @return string HTML renderizado com o resultado do lançamento
     */
    private function iaRoll($value) {
        // Se o valor fornecido for maior que 1 milhão, define como 1 milhão (evita quebrar o valor máximo do mt_rand)
        if($value[1] > 1000000) {
            $total = 1000000;
        } elseif($value[1] < 1) {
            $total = 1;
        } else {
            $total = $value[1];
        }
        $data['result'] = number_format(mt_rand(1, $total), 0, $this->lang['decimals_separator'], $this->lang['thousands_separator']);
        $data['total'] = number_format($total, 0, $this->lang['decimals_separator'], $this->lang['thousands_separator']);

        return $this->view->render($data, 'web/ia/roll');
    }

    /**
     * Gera um código QR para o texto fornecido.
     * 
     * @param array $value Array contendo o texto para gerar o QR Code
     * @return string HTML renderizado com o QR Code
     */
    private function iaQrCode($value) {
        $data['result'] = !empty($value[1]) ? $value[1] : $value[2];
        return $this->view->render($data, 'web/ia/qr_code');
    }

    /**
     * Ordena uma lista de números em ordem crescente ou decrescente.
     * 
     * Processo:
     * 1. Divide a string em números usando espaços, vírgulas ou ponto e vírgula
     * 2. Remove caracteres não numéricos da lista
     * 3. Ordena os números conforme a direção especificada
     * 
     * @param array $value Array contendo a string com números
     * @param int $direction 1 para crescente, 2 para decrescente
     * @return string HTML renderizado com a lista ordenada
     */
    private function iaSort($value, $direction) {
        $data['direction'] = $direction;
        $data['result'] = preg_split('/[\s,;]+/iu', $value[1]);

        // Remove qualquer caractere não numérico da lista
        foreach($data['result'] as $key => $val) {
            if(!is_numeric($val)) {
                unset($data['result'][$key]);
            }
        }

        // Direção da ordenação
        if($direction == 1) {
            sort($data['result']);
        } else {
            rsort($data['result']);
        }

        return $this->view->render($data, 'web/ia/sort');
    }

    /**
     * Inverte o texto fornecido.
     * 
     * @param array $value Array contendo o texto a ser invertido
     * @return string HTML renderizado com o texto invertido
     */
    private function iaReverseText($value) {
        $data['result'] = strrev($value[1]);
        return $this->view->render($data, 'web/ia/reverse_text');
    }

    /**
     * Converte uma cor hexadecimal para diferentes formatos de cor.
     * 
     * Utiliza a biblioteca HexConverter para gerar:
     * - Valor hexadecimal
     * - RGB (Red, Green, Blue)
     * - HSL (Hue, Saturation, Lightness)
     * - CMYK (Cyan, Magenta, Yellow, Key)
     * 
     * @param array $value Array contendo o código hexadecimal da cor
     * @return string HTML renderizado com as conversões de cor
     */
    private function iaHexColor($value) {
        $hex = new HexConverter($value[1]);
        $data['hex'] = $hex->hex();
        $data['rgb'] = $hex->rgb();
        $data['hsl'] = $hex->hsl();
        $data['cmyk'] = $hex->cmyk();

        return $this->view->render($data, 'web/ia/hex_color');
    }

    /**
     * Gera o hash MD5 do texto fornecido.
     * 
     * @param array $value Array contendo o texto para gerar o hash MD5
     * @return string HTML renderizado com o hash MD5
     */
    private function iaMD5($value) {
        $data['query'] = $value[1];
        $data['result'] = md5($value[1]);

        return $this->view->render($data, 'web/ia/md5');
    }

    /**
     * Codifica ou decodifica texto em Base64.
     * 
     * Validações:
     * - Para decodificação, verifica se o resultado é válido
     * - Se a decodificação falhar, retorna false
     * 
     * @param array $value Array contendo o texto para codificar/decodificar
     * @param int $type 1 para codificar, 2 para decodificar
     * @return string|false HTML renderizado com o resultado ou false se falhar
     */
    private function iaBase64($value, $type) {
        $data['query'] = $value[1];
        $data['type'] = $type;

        if($type == 1) {
            $data['result'] = base64_encode($value[1]);
        } else {
            $data['result'] = base64_decode($value[1], true);

            // Se a string decodificada em base64 não for válida
            if(empty(htmlspecialchars($data['result']))) {
                return false;
            }
        }

        return $this->view->render($data, 'web/ia/base64');
    }

    /**
     * Converte o texto para diferentes formatos de case.
     * 
     * Tipos suportados:
     * - 1: Minúsculas (lowercase)
     * - 2: Maiúsculas (uppercase)
     * - 3: Camelcase (primeira palavra minúscula, demais com primeira letra maiúscula)
     * 
     * @param array $value Array contendo o texto para converter
     * @param int $type Tipo de conversão (1, 2 ou 3)
     * @return string HTML renderizado com o texto convertido
     */
    private function iaCase($value, $type) {
        $data['query'] = $value[1];
        $data['type'] = $type;
        if($type == 1) {
            $data['result'] = mb_strtolower($value[1]);
        } elseif($type == 2) {
            $data['result'] = mb_strtoupper($value[1]);
        } else {
            $words = explode(' ', $value[1]);

            $camel = '';

            $i = 1;
            foreach ($words as $word) {
                $camel .= $i > 1 ? ucfirst($word) : strtolower($word);
                $i++;
            }

            $data['result'] = $camel;
        }

        return $this->view->render($data, 'web/ia/case');
    }

    /**
     * Verifica se um ano é bissexto.
     * 
     * Processo:
     * 1. Extrai o ano do array de valores
     * 2. Usa a função date('L') para verificar se é bissexto
     * 3. Define o tipo de resposta (1 para bissexto, 2 para não bissexto)
     * 
     * @param array $value Array contendo o ano a ser verificado
     * @return string HTML renderizado com o resultado da verificação
     */
    private function iaLeapYear($value) {
        if(is_numeric($value[2])) {
            $data['query'] = $value[2];
        } else {
            $data['query'] = $value[4];
        }

        // Verifica se a data é um ano bissexto
        if(date('L', strtotime((int)$data['query'].'-01-01'))) {
            $data['type'] = 1;
        } else {
            $data['type'] = 2;
        }

        return $this->view->render($data, 'web/ia/leap_year');
    }

    /**
     * Exibe a resolução da tela do usuário.
     * 
     * @return string HTML renderizado com a resolução da tela
     */
    private function iaUserScreenResolution() {
        return $this->view->render(null, 'web/ia/user_screen_resolution');
    }

    /**
     * Retorna o valor de Pi (π).
     * 
     * @return string HTML renderizado com o valor de Pi
     */
    private function iaPi() {
        $data['result'] = pi();
        return $this->view->render($data, 'web/ia/pi');
    }

    /**
     * Codifica ou decodifica texto em Código Morse.
     * 
     * Utiliza a biblioteca MorseCode para:
     * - Codificar texto para Morse (type = 1)
     * - Decodificar Morse para texto (type = 2)
     * 
     * @param array $value Array contendo o texto para codificar/decodificar
     * @param int $type 1 para codificar, 2 para decodificar
     * @return string HTML renderizado com o resultado
     */
    private function iaMorseCode($value, $type) {
        $data['query'] = $value[1];

        $mc = new MorseCode($value[1]);

        if($type == 1) {
            $data['result'] = $mc->encode();
        } else {
            $data['result'] = $mc->decode();
        }

        return $this->view->render($data, 'web/ia/morse_code');
    }

    /**
     * Converte tempo Unix para data/hora ou retorna o tempo Unix atual.
     * 
     * Funcionalidades:
     * - Converte timestamp Unix para data e hora (type = 1)
     * - Retorna o timestamp Unix atual (type = 2)
     * 
     * @param array $value Array contendo o timestamp Unix
     * @param int $type 1 para converter, 2 para obter atual
     * @return string HTML renderizado com o resultado
     */
    private function iaUnixTime($value, $type) {
        $data['type'] = $type;
        if($type == 1) {
            $data['query'] = $value[1];
            $data['date'] = explode('-', gmdate('Y-m-d', $value[1]));
            $data['time'] = gmdate('H:i:s', $value[1]);
        } else {
            $data['unix_time'] = time();
        }

        return $this->view->render($data, 'web/ia/unix_time');
    }

    /**
     * Gera texto Lorem Ipsum com o número de parágrafos especificado.
     * 
     * Validações:
     * - Mínimo: 1 parágrafo
     * - Máximo: 50 parágrafos
     * - Padrão: 3 parágrafos se não especificado
     * 
     * Utiliza a biblioteca LoremIpsum para gerar o texto.
     * 
     * @param array $value Array contendo o número de parágrafos desejados
     * @return string HTML renderizado com o texto Lorem Ipsum
     */
    private function iaLoremIpsum($value) {
        $count = 3;
        $max = 50;
        $min = 1;
        if(isset($value[2])) {
            if($value[2] > $max) {
                $count = $max;
            } elseif($value[2] < $min) {
                $count = $min;
            } else {
                $count = $value[2];
            }
        }

        $data['count'] = $count;
        $data['result'] = (new LoremIpsum())->generate($count);

        return $this->view->render($data, 'web/ia/lorem_ipsum');
    }

    /**
     * Aplica a cifra Atbash ao texto fornecido.
     * 
     * A cifra Atbash é uma substituição simples onde:
     * - A = Z, B = Y, C = X, etc.
     * - Números permanecem inalterados
     * - Espaços são adicionados a cada 5 caracteres para melhor legibilidade
     * 
     * @param array $value Array contendo o texto para cifrar
     * @return string HTML renderizado com o texto cifrado
     */
    private function iaAtbashCipher($value) {
        $az = range('a', 'z');
        $za = range('z', 'a');

        $string = strtolower($value[1]);
        $len = strlen($value[1]);
        $encoded = [];

        $count = 0;
        foreach(str_split($string) as $char) {
            $count++;
            // Se o caractere for um dígito
            if(is_numeric($char)) {
                $encoded[] = $char;
            }

            // Obtém o caractere invertido
            if(ctype_alpha($char)) {
                $encoded[] = $za[array_search($char, $az)];
            }

            // Adiciona um espaço a cada 5 caracteres
            if($count % 5 == 0 && $count < $len) {
                $encoded[] = ' ';
            }
        }

        $data['query'] = $value[1];
        $data['result'] = implode('', $encoded);

        return $this->view->render($data, 'web/ia/atbash');
    }

    /**
     * Gera um UUID (Universally Unique Identifier) versão 4.
     * 
     * O UUID gerado segue o padrão RFC 4122:
     * - Formato: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     * - Versão 4 (aleatório)
     * - Variante 1 (RFC 4122)
     * 
     * @param array $value Array de parâmetros (não utilizado)
     * @return string HTML renderizado com o UUID gerado
     */
    private function iaUUID($value)
    {
        $data['result'] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        return $this->view->render($data, 'web/ia/uuid');
    }

    /**
     * Retorna a view dos filtros de busca ou o array de filtros.
     * 
     * Este método gerencia os filtros disponíveis para busca:
     * - Período (último dia, semana, mês)
     * - Tipo de arquivo (PDF, DOC, XLS, etc.)
     * - Busca segura (Off, Moderate, Strict)
     * 
     * Estrutura do array de menu:
     * Array(categoriaTítulo) => Array(parâmetroCategoria), Array(filtrosCategoria), Array(filtroAtual)
     * 
     * @param boolean $type Se true, retorna o array de filtros; se false ou null, retorna a view
     * @return string|array View HTML dos filtros ou array de filtros
     */
    private function searchFilters($type = null) {
        $data['query'] = $_GET['q'];

        /**
         * Mapa do Array: Array(títuloCategoria) => Array(parâmetroCategoria), Array(filtrosCategoria), Array(filtroAtual)
         */
        $data['menu'] = [
            'period'        => [
                ['freshness'],
                [
                    ''          => $this->lang['all'],
                    'Day'       => $this->lang['past_day'],
                    'Week'      => $this->lang['past_week'],
                    'Month'     => $this->lang['past_month']
                ],
                ['']
            ],
            'type'          => [
                ['fileType'],
                [
                    ''          => $this->lang['all'],
                    'doc'       => 'doc',
                    'docx'      => 'docx',
                    'dwf'       => 'dwf',
                    'pdf'       => 'pdf',
                    'ppt'       => 'ppt',
                    'pptx'      => 'pptx',
                    'psd'       => 'psd',
                    'xls'       => 'xls',
                    'xlsx'      => 'xlsx',
                ],
                ['']
            ],
            'safe_search'   => [
                ['safeSearch'],
                [
                    'Off'       => $this->lang['off'],
                    'Moderate'  => $this->lang['moderate'],
                    'Strict'    => $this->lang['strict']
                ],
                ['']
            ]
        ];

        // Os filtros de busca
        $data['filters'] = ['freshness', 'fileType', 'safeSearch'];

        if($type) {
            // Remove campos vazios da lista
            foreach($data['menu'] as $key => $value) {
                unset($data['menu'][$key][1]['']);
            }
            return [$data['menu'], $data['filters']];
        }

        if(isset($_GET['freshness']) && in_array($_GET['freshness'], array_keys($data['menu']['period'][1]))) {
            $data['menu']['period'][2] = $_GET['freshness'];
        } else {
            $data['menu']['period'][2] = '';
        }

        if(isset($_GET['fileType']) && in_array($_GET['fileType'], array_keys($data['menu']['type'][1]))) {
            $data['menu']['type'][2] = $_GET['fileType'];
        } else {
            $data['menu']['type'][2] = '';
        }

        if(isset($_GET['safeSearch']) && in_array($_GET['safeSearch'], array_keys($data['menu']['safe_search'][1]))) {
            $data['menu']['safe_search'][2] = $_GET['safeSearch'];
        } else {
            $data['menu']['safe_search'][2] = $_COOKIE['safe_search'];
        }

        return $this->view->render($data, 'shared/search_filters');
    }
}