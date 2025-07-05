<?php

namespace Fir\Languages;

/**
 * Classe responsável pelo carregamento e gerenciamento dos arquivos de idioma
 * Permite obter traduções e textos internacionalizados para o sistema
 */
class Language {

    /**
     * User selected language
     * @var string
     */
    public $language;

    /**
     * Available languages
     * @var array
     */
    public $languages;

    /**
     * Languages folder path
     * @var string
     */
    public $folder;

    public function __construct($lang = 'english') {
        // Define o idioma padrão
        $this->language = $lang;
        // Carrega o arquivo de idioma correspondente
        $this->loadLanguageFile();
    }

    private function loadLanguageFile() {
        // Monta o caminho do arquivo de idioma
        $this->folder = __DIR__ .'/../languages/';
        // Verifica se o arquivo existe
        if(file_exists($this->folder.$this->language.'.php')) {
            // Inclui o arquivo de idioma
        $this->languages = $this->languages();
        } else {
            // Caso não exista, define o dicionário como vazio
            $this->languages = [];
        }
    }

    /**
     * Get all the available languages from the languages folder
     * @return	array
     */
    private function languages() {
        $languages = [];

        if($handle = opendir($this->folder)) {
            while(false !== ($entry = readdir($handle))) {
                if($entry != '.' && $entry != '..' && substr($entry, -4, 4) == '.php') {
                    $name = pathinfo($entry);
                    $languages[] = $name['filename'];
                }
            }
            closedir($handle);
        }

        return $languages;
    }

    /**
     * Retorna o idioma atualmente selecionado
     * @return string
     */
    public function get() {
        return $this->language;
    }

    /**
     * Retorna a tradução correspondente à chave, ou a própria chave caso não exista
     * @param string $key
     * @return string
     */
    public function traduzir($key) {
        // Garante que o dicionário de idioma está carregado
        if (!isset($this->dictionary)) {
            $this->carregarDicionario();
        }
        // Retorna a tradução ou a própria chave se não existir
        return isset($this->dictionary[$key]) ? $this->dictionary[$key] : $key;
    }

    /**
     * Carrega o dicionário de traduções do idioma selecionado
     */
    private function carregarDicionario() {
        $arquivo = $this->folder . $this->language . '.php';
        if (file_exists($arquivo)) {
            $this->dictionary = include($arquivo);
        } else {
            $this->dictionary = [];
        }
    }

    /**
     * Set and select the language file
     *
     * @param   string  $language   The default site language
     * @return	string | array
     */
    public function set($language = null) {
        // If the user wants to set a language
        if(isset($_POST['site_language'])) {
            // If the language exists
            if(in_array($_POST['site_language'], $this->languages)) {
                $language = $_POST['site_language'];
                setcookie('lang', $language, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH); // Expire in one month
            } else {
                // Set the language to the default one
                setcookie('lang', $language, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH); // Expire in one month
            }
        }
        // If the user has previously selected a language
        elseif(isset($_COOKIE['lang'])) {
            // Verify if the selected language exists
            if(in_array($_COOKIE['lang'], $this->languages)) {
                $language = $_COOKIE['lang'];
            }
        }
        // Set the language to the default one
        else {
            setcookie('lang', $language, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH); // Expire in one month
        }
        // Store the selected language
        $this->language = $language;

        // If the language exists, load and return its content
        if(in_array($language, $this->languages)) {
            require_once($this->folder.$language.'.php');
            return $lang;
        }
    }
}