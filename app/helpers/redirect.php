<?php

/**
 * Redireciona para uma rota interna do sistema
 *
 * @param   string  $path   Caminho interno para redirecionamento
 */
function redirect($path = null) {
    // Envia o cabeçalho HTTP para redirecionar o navegador para a nova URL
    header('Location: ' . URL_PATH . '/' . $path);

    /**
     * O exit é necessário para interromper a execução de qualquer código após o redirecionamento
     * Isso também garante que variáveis de sessão sejam passadas corretamente para a página de destino
     */
    exit;
}