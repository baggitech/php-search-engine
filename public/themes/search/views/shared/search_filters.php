<?php
defined('FIR') OR exit();
/**
 * Template para exibição dos Filtros de Busca
 * 
 * Este arquivo define a interface dos filtros de busca disponíveis:
 * - Período (último dia, semana, mês)
 * - Tipo de arquivo (PDF, DOC, XLS, etc.)
 * - Busca segura (Off, Moderate, Strict)
 * 
 * Funcionalidades:
 * - Menu dropdown para cada categoria de filtro
 * - Destaca o filtro ativo
 * - Mantém outros filtros ao trocar categoria
 * - Suporte a RTL (right-to-left) para idiomas árabes
 * - Scroll horizontal para múltiplos filtros
 * - URLs dinâmicas com parâmetros de filtro
 */
?>
<div class="page-menu page-menu-left filters-menu">
    <!-- Container dos filtros com scroll horizontal -->
    <div class="row row-dragscroll dragscroll filters-fade-<?php if($lang['lang_dir'] == 'rtl'): ?>left<?php else: ?>right<?php endif ?>">
        <!-- Loop através das categorias de filtros -->
        <?php foreach($data['menu'] as $tKey => $value): ?>
            <!-- Elemento de filtro individual -->
            <div class="filter-element" id="<?=$tKey?>"><?=$lang[$tKey]?>: <strong><?=($value[1][$value[2]])?></strong><div class="down-arrow"></div>
                <!-- Lista dropdown de opções do filtro -->
                <div class="filter-list">
                    <!-- Loop através das opções de cada filtro -->
                    <?php foreach($value[1] as $fKey => $filter): ?>
                        <a href="<?=$data['url'].'/'.e($this->url[0]).'?q='.e($data['query'])?><?php foreach($data['filters'] as $f): ?><?php if($f == $value[0][0]): ?>&<?=$f?>=<?=$fKey?><?php else: ?><?=(isset($_GET[$f]) ? '&'.$f.'='.e($_GET[$f]) : '')?><?php endif ?><?php endforeach ?>"<?=(($fKey == $value[2]) ? ' class="menu-active"' : '')?>><?=$filter?></a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="divider"></div>
</div>