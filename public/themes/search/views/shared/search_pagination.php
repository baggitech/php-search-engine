<?php
defined('FIR') OR exit();
/**
 * Template para exibição dos botões de paginação
 * 
 * Este arquivo define a interface de navegação entre páginas de resultados:
 * - Botão "Primeira página"
 * - Botão "Página anterior"
 * - Botão "Próxima página"
 * - Informações sobre resultados
 * - Créditos da API de busca
 * 
 * Funcionalidades:
 * - Navegação entre páginas mantendo filtros ativos
 * - Botões desabilitados quando não há mais páginas
 * - Contador de resultados estimados
 * - Links para política de privacidade da API
 * - URLs dinâmicas com parâmetros de busca e filtros
 */
?>
<div class="results-pagination">
    <!-- Botão "Primeira página" -->
    <?php if($data['prev_button']): ?>
        <a href="<?=$data['url'].'/'.e($this->url[0]).'?q='.e($data['query'])?><?php foreach($data['filters'] as $filter): ?><?=(isset($_GET[$filter]) ? '&'.$filter.'='.e($_GET[$filter]) : '')?><?php endforeach ?>"><div class="button button-pagination pagination-home"></div></a>
    <?php else: ?>
        <div class="button button-pagination button-margin-right button-disabled pagination-home"></div>
    <?php endif ?>

    <!-- Botão "Página anterior" -->
    <?php if($data['prev_button']): ?>
        <a href="<?=$data['url'].'/'.e($this->url[0]).'?q='.e($data['query'])?><?php foreach($data['filters'] as $filter): ?><?=(isset($_GET[$filter]) ? '&'.$filter.'='.e($_GET[$filter]) : '')?><?php endforeach ?>&offset=<?=e($data['current_page']-$data['per_page'])?>"><div class="button button-pagination pagination-prev"></div></a>
    <?php else: ?>
        <div class="button button-pagination button-margin-right button-disabled pagination-prev"></div>
    <?php endif ?>

    <!-- Botão "Próxima página" -->
    <?php if($data['next_button']): ?>
        <a href="<?=$data['url'].'/'.e($this->url[0]).'?q='.e($data['query'])?><?php foreach($data['filters'] as $filter): ?><?=(isset($_GET[$filter]) ? '&'.$filter.'='.e($_GET[$filter]) : '')?><?php endforeach ?>&offset=<?=e($data['current_page']+$data['per_page'])?>"><div class="button button-pagination pagination-next"></div></a>
    <?php else: ?>
        <div class="button button-pagination button-disabled pagination-next"></div>
    <?php endif ?>

    <!-- Informações adicionais da paginação -->
    <div class="pagination-details">
        <!-- Créditos da API de busca -->
        <div class="results-by"><a href="https://privacy.microsoft.com/en-us/privacystatement" target="_blank" data-nd>Results by Bing</a></div>
        
        <!-- Contador de resultados estimados -->
        <div class="x-results"><?=sprintf($lang['x_results'], number_format($data['estimated_results'], 0, $this->lang['decimals_separator'], $this->lang['thousands_separator']))?></div>
    </div>
</div>