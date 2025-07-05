<?php
defined('FIR') OR exit();
/**
 * Template para exibição do Menu de Busca
 * 
 * Este arquivo define o menu de navegação entre diferentes tipos de busca:
 * - Web (sites)
 * - Imagens
 * - Vídeos
 * - Notícias
 * 
 * Funcionalidades:
 * - Menu horizontal com scroll arrastável
 * - Destaca o tipo de busca ativo
 * - Suporte a RTL (right-to-left) para idiomas árabes
 * - Botão de filtros para refinar resultados
 * - Links diretos para cada tipo de busca com a consulta atual
 */
?>
<div class="page-menu page-menu-left" id="page-menu">
    <div class="search-type-menu">
        <!-- Menu horizontal com scroll arrastável -->
        <div class="row row-dragscroll dragscroll filters-fade-<?php if($lang['lang_dir'] == 'rtl'): ?>left<?php else: ?>right<?php endif ?>">
            <!-- Loop através dos tipos de busca disponíveis -->
            <?php foreach($data['menu'] as $key => $value): ?>
                <a href="<?=$data['url'].'/'.e($key).'?q='.e($data['query'])?>"<?=(($value[0] == $this->url[0]) ? ' class="menu-active"' : '')?>><?=$lang[$key]?></a>
            <?php endforeach ?>
            
            <!-- Botão de filtros para refinar resultados -->
            <div class="filters-toggle">
                <?=$lang['filters']?>
            </div>
        </div>
    </div>
    <div class="divider"></div>
</div>