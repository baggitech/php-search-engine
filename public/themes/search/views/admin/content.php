<?php
defined('FIR') OR exit();
/**
 * Template para exibição do conteúdo do Painel Administrativo
 * 
 * Este arquivo define a estrutura principal do painel administrativo:
 * - Menu lateral de navegação
 * - Cabeçalho da página com título
 * - Área de conteúdo principal
 * - View específica de cada seção
 * 
 * Funcionalidades:
 * - Layout consistente para todas as páginas do admin
 * - Título dinâmico baseado na página atual
 * - Menu lateral para navegação entre seções
 * - Área de conteúdo flexível para diferentes views
 * - Design responsivo para administradores
 */
?>
<div id="content" class="content content-<?=e($this->url[0])?>">
    <!-- Menu lateral de navegação do admin -->
    <?=$data['menu_view']?>
    
    <!-- Cabeçalho da página com título -->
    <div class="page-header">
        <div class="row">
            <div class="page-title"><?=e((is_array($data['page_title']) ? implode(' - ', $data['page_title']) : $data['page_title']))?></div>
        </div>
    </div>
    
    <!-- Área de conteúdo principal -->
    <div class="page-content">
        <div class="row">
            <!-- View específica de cada seção do admin -->
            <?=$data['settings_view']?>
        </div>
    </div>
</div>