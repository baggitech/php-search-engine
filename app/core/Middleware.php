<?php

namespace Fir\Middleware;

/**
 * Classe base para middlewares do sistema
 * Permite interceptar requisições e aplicar regras antes de chegar ao controller
 */
class Middleware {

    /**
     * @var array   The list of routes to be excluded from being affected by middleware
     *              Array Map: Key(Middleware) => Array(Routes)
     */
    protected $except = [
        'CsrfToken'     => [],
        'Authorize'     => [],
        'UserSettings'  => []
    ];

    /**
     * Middleware to be loaded
     * @var array
     */
    private $middleware = [];

    public function __construct() {
        // Inicializa o array de middlewares
        $this->middlewares = [];
        $this->getAll();
        foreach($this->middleware as $name) {
            // If a middleware exception exists
            if(isset($this->except[$name])) {
                foreach($this->except[$name] as $route) {
                    // If the route has match anything rule (*)
                    if(substr($route, -1) == '*') {
                        // If the current path matches a route exception
                        if(stripos($_GET['url'], str_replace('*', '', $route)) === 0) {
                            return;
                        }
                    }
                    // If the current path matches a route exception
                    elseif(isset($_GET['url']) && $_GET['url'] == $route) {
                        return;
                    }
                }
            }
            require_once(__DIR__ . '/../middleware/' . $name . '.php');

            $class = 'Fir\Middleware\\' . $name;

            new $class;
        }
    }

    private function getAll() {
        if($handle = opendir(__DIR__ . '/../middleware/')) {
            while(false !== ($entry = readdir($handle))) {
                if($entry != '.' && $entry != '..' && substr($entry, -4, 4) == '.php') {
                    $name = pathinfo($entry);
                    $this->middleware[] = $name['filename'];
                }
            }
            closedir($handle);
        }
    }

    public function add($middleware) {
        // Adiciona um middleware ao array
        $this->middlewares[] = $middleware;
    }

    public function run($request) {
        // Executa todos os middlewares registrados
        foreach ($this->middlewares as $middleware) {
            // Instancia o middleware e executa o método handle
            $instance = new $middleware();
            $instance->handle($request);
        }
    }
}