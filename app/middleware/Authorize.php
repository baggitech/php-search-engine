<?php

namespace Fir\Middleware;

/**
 * Classe Authorize cuida do roteamento do site com base no status do usuário
 */
class Authorize {

    /**
     * Lista de rotas a serem bloqueadas para determinados perfis de usuário
     * Mapa: Chave (perfil) => Array(Mapas de Rotas) => (Array(Rotas), Array(Redirecionamento))
     * Exemplo: 'guest' não pode acessar rotas administrativas, 'admin' não pode acessar login admin
     * @var array
     */
    protected $except = [
        'guest' => [
            'admin' => [['admin/dashboard*', 'admin/general*', 'admin/appearance*', 'admin/search*', 'admin/themes*', 'admin/languages*', 'admin/info_pages*', 'admin/ads*', 'admin/password*'], ['admin/login']]
        ],
        'admin' => [
            'admin' => [['admin', 'admin/login*'], ['admin/dashboard']]
        ]
    ];

    /**
     * Construtor: verifica o perfil do usuário e redireciona caso tente acessar rota bloqueada
     */
    public function __construct() {
        // Seleciona o perfil do usuário com base na sessão
        if(isset($_SESSION['isAdmin'])) {
            $user = 'admin';
        } else {
            $user = 'guest';
        }

        // Para cada grupo de rotas bloqueadas para o perfil
        foreach($this->except[$user] as $routes) {
            // Para cada rota bloqueada
            foreach($routes[0] as $route) {
                // Se a rota termina com * (coringa)
                if(substr($route, -1) == '*') {
                    // Se a URL atual começa com o prefixo da rota bloqueada
                    if(isset($_GET['url']) && stripos($_GET['url'], str_replace('*', '', $route)) === 0) {
                        redirect($routes[1][0]); // Redireciona para rota permitida
                    }
                }
                // Se a URL atual corresponde exatamente a uma rota bloqueada
                elseif(isset($_GET['url']) && in_array($_GET['url'], $routes[0])) {
                    redirect($routes[1][0]); // Redireciona para rota permitida
                }
            }
        }
    }
}