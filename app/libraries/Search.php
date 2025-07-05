<?php

namespace Fir\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

class Search {

    /**
     * Chave da API do Bing
     * @var string
     */
    public $key;

    /**
     * Endpoint da API
     * @var string
     */
    public $endpoint;

    /**
     * Filtro de resposta da API
     * @var string
     */
    public $responseFilter;

    /**
     * Lista de mercados disponíveis para busca (idioma e país)
     * @var array
     */
    private $markets = ['es-AR' => ['Argentina', 'Spanish'], 'en-AU' => ['Australia', 'English'], 'de-AT' => ['Austria', 'German'], 'nl-BE' => ['Belgium', 'Dutch'], 'fr-BE' => ['Belgium', 'French'], 'pt-BR' => ['Brazil', 'Portuguese'], 'en-CA' => ['Canada', 'English'], 'fr-CA' => ['Canada', 'French'], 'es-CL' => ['Chile', 'Spanish'], 'da-DK' => ['Denmark', 'Danish'], 'fi-FI' => ['Finland', 'Finnish'], 'fr-FR' => ['France', 'French'], 'de-DE' => ['Germany', 'German'], 'zh-HK' => ['Hong Kong SAR', 'Traditional Chinese'], 'en-IN' => ['India', 'English'], 'en-ID' => ['Indonesia', 'English'], 'it-IT' => ['Italy', 'Italian'], 'ja-JP' => ['Japan', 'Japanese'], 'ko-KR' => ['Korea', 'Korean'], 'en-MY' => ['Malaysia', 'English'], 'es-MX' => ['Mexico', 'Spanish'], 'nl-NL' => ['Netherlands', 'Dutch'], 'en-NZ' => ['New Zealand', 'English'], 'no-NO' => ['Norway', 'Norwegian'], 'zh-CN' => ['People\'s republic of China', 'Chinese'], 'pl-PL' => ['Poland', 'Polish'], 'pt-PT' => ['Portugal', 'Portuguese'], 'en-PH' => ['Republic of the Philippines', 'English'], 'ru-RU' => ['Russia', 'Russian'], 'ar-SA' => ['Saudi Arabia', 'Arabic'], 'en-ZA' => ['South Africa', 'English'], 'es-ES' => ['Spain', 'Spanish'], 'sv-SE' => ['Sweden', 'Swedish'], 'fr-CH' => ['Switzerland', 'French'], 'de-CH' => ['Switzerland', 'German'], 'zh-TW' => ['Taiwan', 'Traditional Chinese'], 'tr-TR' => ['Turkey', 'Turkish'], 'en-GB' => ['United Kingdom', 'English'], 'en-US' => ['United States', 'English'], 'es-US' => ['United States', 'Spanish']];

    /**
     * Realiza uma requisição à API do Bing com os parâmetros informados
     *
     * @param   array   $params Parâmetros da requisição
     * @return  string  Resposta da API ou mensagem de erro
     */
    public function request($params) {
        // Cria um cliente HTTP usando Guzzle
        $httpClient = new Client(['http_errors' => false]);
        try {
            // Monta e executa a requisição GET para a API do Bing
            $request = $httpClient->request('GET', 'https://api.bing.microsoft.com/v7.0/'.$this->endpoint.'?'.http_build_query($params).(isset($this->responseFilter) ? '&responseFilter='.$this->responseFilter : ''), [
                'headers' => ['Ocp-Apim-Subscription-Key' => $this->key]
            ]);
            // Obtém o conteúdo da resposta
            $output = $request->getBody()->getContents();
        } catch (\Exception $e) {
            // Em caso de erro, retorna a mensagem de exceção
            $output = $e->getMessage();
        }
        // Retorna o resultado da requisição
        return $output;
    }

    /**
     * Retorna a lista de mercados disponíveis
     *
     * @return  array
     */
    public function getMarkets() {
        return $this->markets;
    }

    /**
     * Monta a sintaxe de consulta para o operador "site:"
     *
     * @param   string  $domains Lista de domínios (um por linha)
     * @return  string  Sintaxe para busca restrita a domínios
     */
    public function specificSites($domains) {
        $output = '';
        // Se a lista de domínios não estiver vazia
        if(!empty($domains)) {
            // Remove http(s):// e separa por linhas
            $domains = explode(PHP_EOL, str_replace(array('http://', 'https://'), '', $domains));
            $urls = [];
            // Para cada domínio, monta a expressão site:dominio
            foreach($domains as $domain) {
                $urls[] = 'site:'.rtrim($domain, '/');
            }
            // Junta as expressões com OR para busca múltipla
            $output = ' ('.implode(' OR ', $urls).')';
        }
        // Retorna a sintaxe de busca restrita
        return $output;
    }
}