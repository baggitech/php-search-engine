<?php

namespace Fir;

/**
 * Classe principal responsável pelo roteamento e inicialização da aplicação
 * Interpreta a URL, carrega o controller e executa a ação correspondente
 */
class App {
    /**
     * Default controller if none is specified
     * @var	string
     */
    protected $controller = 'home';

    /**
     * Default method if none is specified
     * @var string
     */
    protected $method = 'index';

    /**
     * List of GET parameters sent by the user
     * @var array
     */
    protected $url = [];

    /**
     * App constructor.
     */
    public function __construct() {
        // Cria a conexão com o banco de dados
        $this->db = (new Connection\Database())->connect();

        // Carrega dependências do Composer (vendor)
        $this->load(2);

        // Carrega bibliotecas
        $this->load(1);

        // Carrega helpers
        $this->load(0);

        // Instancia o middleware (executa regras globais antes dos controllers)
        new Middleware\Middleware();

        // Faz o parsing da URL recebida
        $this->parseUrl();

        // Verifica se o controller solicitado existe
        if(isset($this->url[0])) {
            if(file_exists(__DIR__ . '/../controllers/'. $this->url[0].'.php')) {
                // Define o controller a ser usado
                $this->controller = $this->url[0];
            } elseif(!empty($this->url[0])) {
                // Se não existir, redireciona para a home
                redirect();
            }
        }
        // Inclui o arquivo do controller
        require_once(__DIR__ . '/../controllers/'. $this->controller .'.php');

        // Instancia a classe do controller
        $class = 'Fir\\Controllers\\'.$this->controller;
        $this->controller = new $class($this->db, $this->url);

        // Verifica se há um método específico na URL e se ele existe no controller
        if(isset($this->url[1])) {
            if(method_exists($this->controller, $this->url[1])) {
                $this->method = $this->url[1];
            }
        }

        // Executa o método do controller, passando os parâmetros da URL
        $data = call_user_func_array([$this->controller, $this->method], $this->url);

        // Executa o método run do controller (padrão para renderização)
        $this->controller->run($data);

        // Fecha a conexão com o banco de dados
        $this->db->close();
    }

    /**
     * Faz o parsing e define os parâmetros GET enviados pelo usuário
     */
    public function parseUrl() {
        if(isset($_GET['url'])) {
            // Quebra a URL em partes separadas por /
            $this->url = explode('/', rtrim($_GET['url'], '/'));
        }
    }

    /**
     * Carrega as bibliotecas e helpers
     *
     * @param   int     $type   0 para helpers, 1 para bibliotecas, 2 para dependências do Composer
     * @return  object
     */
    private function load($type) {
        if($type == 2) {
            // Carrega o autoload do Composer
            if(file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once(__DIR__ . '/../vendor/autoload.php');
            }
        } elseif($type == 1) {
            // Autoload de bibliotecas conforme necessário
            spl_autoload_register(function($class) {
                // Pega apenas o nome da classe (sem namespace)
                $className = explode('\\', $class);
                if(file_exists(__DIR__ . '/../libraries/'.end($className).'.php')) {
                    require_once(__DIR__ . '/../libraries/'.end($className).'.php');
                }
            });
        } else {
            // Carrega todos os helpers automaticamente
            if($handle = opendir(__DIR__ . '/../helpers/')) {
                while(false !== ($entry = readdir($handle))) {
                    if($entry != '.' && $entry != '..' && substr($entry, -4, 4) == '.php') {
                        require_once(__DIR__ . '/../helpers/'.$entry);
                    }
                }
                closedir($handle);
            }
        }
    }
}