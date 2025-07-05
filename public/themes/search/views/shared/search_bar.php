<?php
defined('FIR') OR exit();
/**
 * Template para exibição da Barra de Busca
 * 
 * Este arquivo define a interface da barra de busca principal, incluindo:
 * - Campo de entrada de texto para consultas
 * - Botão de limpar busca
 * - Botão de executar busca
 * - Lista de sugestões de busca
 * 
 * Funcionalidades:
 * - Autocompletar desabilitado para melhor controle
 * - Sugestões de busca em tempo real
 * - Suporte a diferentes caminhos de busca (web, imagens, vídeos, etc.)
 * - Token CSRF para segurança
 * - Foco automático configurável
 */
?>
<div class="search-content<?=(isset($data['top_bar']) ? ' search-content-s' : '')?>">
    <div class="search-container">
        <!-- Campo de entrada principal da busca -->
        <input type="text" name="search" id="search-input" class="search-input" tabindex="1" autocomplete="off" autocapitalize="off" autocorrect="off" data-search-url="<?=$data['url']?>/" data-search-path="<?=$data['query_path']?>" data-suggestions-path="requests/suggestions" value="<?=e(isset($data['query']) ? $data['query'] : '')?>" data-token-id="<?=$_SESSION['token_id']?>" data-autofocus="<?=$data['autofocus']?>" data-suggestions="<?=$data['settings']['search_suggestions']?>">
        
        <!-- Botão para limpar o campo de busca -->
        <div id="clear-button" class="clear-button"></div>
        
        <!-- Botão para executar a busca -->
        <div id="search-button" class="search-button"></div>
        
        <!-- Container para lista de sugestões -->
        <div class="search-list">
            <div class="search-list-container" id="search-results"></div>
        </div>
    </div>
</div>