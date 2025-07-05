<?php

namespace Fir\Views;

/**
 * Classe responsável pelo gerenciamento e renderização das views (templates)
 * Permite passar dados para as views e renderizar arquivos de template
 */
class View {

    /**
     * The site settings from the DB
     * @var array
     */
    protected $settings;

    /**
     * The language array
     * @var array
     */
    protected $lang;

    /**
     * The current URL path (route) array to be passed to the controllers
     * @var array
     */
    public $url;

    /**
     * The site metadata
     * @var array   An array containing various metadata attributes
     *              Array Map: Metadata => Mixed(Values)
     */
    public $metadata;

    public function __construct($settings, $language, $url) {
        $this->settings = $settings;
        $this->lang = $language;
        $this->url = $url;
    }

    /**
     * @param	array	$data	The data to be passed to the view template
     * @param	string	$view	The file path / name of the view
     * @return	string
     */
    public function render($data = [], $view = null) {
        // Variáveis globais usadas em todas as views
        $data['settings']       = $this->settings;
        $data['year']           = date('Y');
        $data['url']            = URL_PATH;
        $data['theme_path']     = THEME_PATH;
        $data['cookie']         = $_COOKIE;
        $lang = $this->lang;

        // Inicia o buffer de saída para permitir herança de templates
        ob_start();

        // Inclui o arquivo da view (template)
        require(sprintf('%s/../../%s/%s/%s/views/%s.php', __DIR__, PUBLIC_PATH, THEME_PATH, $this->settings['site_theme'], $view));

        // Retorna o conteúdo renderizado
        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function message() {
        $messages = null;
        // Se existe mensagem na sessão
        if(isset($_SESSION['message'])) {
            // Renderiza cada mensagem
            foreach($_SESSION['message'] as $key => $value) {
                $data['message'] = ['type' => $value[0], 'content' => $value[1]];
                $messages .= $this->render($data, 'shared/message');
            }
        }
        // Remove as mensagens da sessão após exibir
        unset($_SESSION['message']);
        return $messages;
    }

    /**
     * @return string
     */
    public function token() {
        // Renderiza o token CSRF salvo na sessão
        $data['token_id'] = $_SESSION['token_id'];
        return $this->render($data, 'shared/token');
    }

    /**
     * @return string
     */
    public function docTitle() {
        // Monta o título da página (usando metadados se disponíveis)
        if(isset($this->metadata['title'])) {
            $title = implode(' - ', $this->metadata['title']).' - '.$this->settings['site_title'];
        } else {
            $title = $this->settings['site_title'];
        }
        return $title;
    }
}