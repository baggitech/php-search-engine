<?php

namespace Fir\Libraries;

class Session {

    /**
     * Define um valor na sessão para a chave informada
     * @param string $key   Nome da chave na sessão
     * @param mixed  $value Valor a ser armazenado
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Recupera o valor de uma chave da sessão, se existir
     * @param string $key Nome da chave na sessão
     * @return mixed|null Valor armazenado ou null se não existir
     */
    public static function get($key) {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    
    /**
     * Destroi toda a sessão do usuário
     */
    public static function destroy() {
        session_destroy();
    }
}