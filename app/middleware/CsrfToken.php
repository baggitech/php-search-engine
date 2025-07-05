<?php

namespace Fir\Middleware;

use Fir\Languages\Language as Language;

/**
 * Classe CsrfToken garante que todas as requisições POST tenham um token CSRF válido
 */
class CsrfToken {

    /**
     * Construtor: gera e valida o token CSRF
     */
    public function __construct() {
        $this->generateToken();
        $this->validateToken();
    }

    /**
     * Valida o token CSRF enviado no formulário
     */
    private function validateToken() {
        // Se houver dados enviados via POST
        if(isset($_POST) && !empty($_POST)) {
            // Compara o token enviado com o token salvo na sessão
            if($_POST['token_id'] != $_SESSION['token_id']) {
                $lang = (new Language())->set();
                // Adiciona mensagem de erro na sessão
                $_SESSION['message'][] = ['error', $lang['token_mismatch']];
                // Redireciona para a mesma página
                header("Location: " . URL_PATH . '/' . $_GET['url']);
                // Impede a execução de qualquer outro código
                exit;
            }
        }
    }

    /**
     * Gera e armazena um token aleatório na sessão para proteção CSRF
     */
    private function generateToken() {
        // Se não existe token na sessão ou está vazio
        if(!isset($_SESSION['token_id']) || empty($_SESSION['token_id'])) {
            // Gera um token aleatório usando hash sha256
            $token_id = hash('sha256', substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10));
            // Salva o token na sessão
            $_SESSION['token_id'] = $token_id;
        }
    }
}