<?php

namespace Fir\Libraries;

class Input {

    /**
     * Retorna o valor de um parâmetro informado na URL amigável
     *
     * @param   string  $param  O parâmetro a ser buscado na URL
     * @return  string | bool   Valor do parâmetro ou false se não encontrado
     */
    public static function get($param) {
        // Verifica se existe o parâmetro 'url' na query string
        if(isset($_GET['url'])) {
            // Quebra a URL em partes separadas por '/'
            $url = explode('/', rtrim($_GET['url'], '/'));

            // Procura o índice do parâmetro na URL
            $pId = array_search($param, $url);

            // Se encontrou o parâmetro
            if($pId !== false) {
                // Retorna o valor imediatamente após o parâmetro
                if(isset($url[$pId+1])) {
                    return $url[$pId+1];
                } else {
                    // Se não houver valor após o parâmetro, retorna false
                    return false;
                }
            } else {
                // Se não encontrou o parâmetro, retorna false
                return false;
            }
        } else {
            // Se não existe parâmetro 'url', retorna false
            return false;
        }
    }
}