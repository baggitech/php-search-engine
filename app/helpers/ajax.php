<?php

/**
 * Verifica se a requisição é dinâmica (ajax)
 *
 * @return  boolean
 */
function isAjax() {
    // Verifica se o cabeçalho HTTP_X_REQUESTED_WITH está presente e se é igual a 'xmlhttprequest'
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        // || isset($_GET['live']) // Comentado: poderia ser usado para identificar requisições "live"
        ) {
        // Se for uma requisição AJAX, retorna verdadeiro
        return true;
    } else {
        // Caso contrário, retorna falso
        return false;
    }
}