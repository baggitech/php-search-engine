<?php
defined('FIR') OR exit();
/**
 * Template para exibição do conteúdo da página de busca Web
 * 
 * Este arquivo define a estrutura principal da página de resultados de busca web, incluindo:
 * - Menu de navegação
 * - Filtros de busca
 * - Container de resultados principais
 * - Barra lateral com entidades
 * - Respostas instantâneas
 * 
 * Layout:
 * - Menu superior para navegação entre tipos de busca
 * - Filtros para refinar resultados
 * - Área principal com resultados e respostas instantâneas
 * - Sidebar com entidades relacionadas (se disponível)
 */
?>
<div id="content" class="content content-<?=e($this->url[0])?>">
    <!-- Menu de navegação entre tipos de busca -->
    <?=$data['menu_view']?>
    
    <!-- Filtros de busca (período, tipo de arquivo, safe search) -->
    <?=$data['filters_view']?>
    
    <!-- Container principal dos resultados -->
    <div class="results-container">
        <!-- Conteúdo principal dos resultados -->
        <div class="results-content">
            <!-- Respostas instantâneas (calculadora, conversões, etc.) -->
            <?=$data['result_ia_view']?>
            
            <!-- Resultados da busca web -->
            <?=$data['search_results_view']?>
        </div>
        
        <!-- Barra lateral com entidades relacionadas -->
        <div class="results-sidebar">
            <?=$data['entities_view']?>
        </div>
    </div>
</div>