<?php
defined('FIR') OR exit();
/**
 * Template para exibição do conteúdo da página de Preferências
 * 
 * Este arquivo define a estrutura principal da página de preferências do usuário:
 * - Menu lateral de navegação
 * - Cabeçalho da página com título
 * - Área de conteúdo principal
 * - View específica de cada tipo de preferência
 * 
 * Funcionalidades:
 * - Layout consistente para todas as páginas de preferências
 * - Menu lateral para navegação entre tipos de preferências
 * - Título dinâmico baseado na seção atual
 * - Área de conteúdo flexível para diferentes views
 * - Design responsivo para configurações do usuário
 */
?>
<div id="content" class="content content-<?=e($this->url[0])?>">
    <!-- Menu lateral de navegação das preferências -->
    <?=$data['menu_view']?>
    
    <!-- Cabeçalho da página com título -->
    <div class="page-header">
        <div class="row">
            <div class="page-title"><?=e($data['page_title'])?></div>
        </div>
    </div>
    
    <!-- Área de conteúdo principal -->
    <div class="page-content">
        <div class="row">
            <!-- View específica de cada tipo de preferência -->
            <?=$data['preferences_view']?>
        </div>
    </div>
</div>