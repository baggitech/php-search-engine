<?php
defined('FIR') OR exit();
/**
 * Template para exibição do conteúdo da página de busca de Imagens
 * 
 * Este arquivo define a estrutura principal da página de resultados de imagens:
 * - Menu de navegação entre tipos de busca
 * - Filtros específicos para imagens
 * - Resultados de busca de imagens
 * 
 * Funcionalidades:
 * - Layout consistente com outras páginas de busca
 * - Filtros específicos para imagens (tamanho, cor, tipo, etc.)
 * - Menu de navegação para alternar entre tipos de busca
 * - Design responsivo
 */
?>
<div id="content" class="content content-<?=e($this->url[0])?>">
    <!-- Menu de navegação entre tipos de busca -->
    <?=$data['menu_view']?>
    
    <!-- Filtros específicos para busca de imagens -->
    <?=$data['filters_view']?>
    
    <!-- Resultados da busca de imagens -->
    <?=$data['search_results_view']?>
</div>