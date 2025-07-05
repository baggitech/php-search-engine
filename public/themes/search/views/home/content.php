<?php
defined('FIR') OR exit();
/**
 * Template para exibição do conteúdo da página inicial (Home)
 * 
 * Este arquivo define a estrutura principal da página inicial:
 * - Logo grande do site
 * - Barra de busca centralizada
 * - Tagline/descrição do site
 * - Anúncios (se habilitados)
 * 
 * Funcionalidades:
 * - Logo adaptativo (claro/escuro baseado no tema)
 * - Barra de busca como elemento central
 * - Suporte a fundos de tela
 * - Anúncios configuráveis
 * - Layout responsivo e centralizado
 */
?>
<div id="content" class="content content-home">
    <div class="home-center">
        <!-- Logo grande do site com link para home -->
        <div class="home-logo"><div class="logo-large"><a href="<?=$data['url']?>/"><img src="<?=$data['url']?>/<?=UPLOADS_PATH?>/brand/<?php if($data['cookie']['dark_mode']): ?><?=$data['settings']['logo_large_dark']?><?php else: ?><?php if($data['settings']['site_backgrounds'] && $data['cookie']['backgrounds']): ?><?=$data['settings']['logo_large_dark']?><?php else: ?><?=$data['settings']['logo_large']?><?php endif ?><?php endif ?>"></a></div></div>
        
        <!-- Barra de busca principal -->
        <?=$data['search_bar_view']?>
        
        <!-- Descrição/tagline do site -->
        <div class="home-description"><?=e($data['settings']['site_tagline'])?></div>
        
        <!-- Anúncios da página inicial (se habilitados) -->
        <?=($data['show_ads'] ? $data['settings']['ads_1'] : false)?>
    </div>
</div>