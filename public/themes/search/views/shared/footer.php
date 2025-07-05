<?php
defined('FIR') OR exit();
/**
 * Template para exibição da seção Footer (rodapé)
 * 
 * Este arquivo define a estrutura do rodapé do site, incluindo:
 * - Menu de páginas institucionais
 * - Link para painel administrativo (se usuário logado)
 * - Informações de copyright
 * - Créditos do software
 * 
 * Funcionalidades:
 * - Exibe páginas institucionais configuradas no admin
 * - Destaca página ativa no menu
 * - Mostra link do admin apenas para usuários logados
 * - Informações de copyright dinâmicas
 * - Responsivo (alguns elementos apenas desktop)
 */
?>
<footer id="footer" class="footer<?php if(!$this->url[0]): ?> footer-home<?php endif ?>">
    <div class="footer-content">
        <!-- Menu de páginas institucionais -->
        <div class="footer-menu">
            <?php foreach($data['info_pages'] as $value): ?>
                <div class="footer-element"><a href="<?=$data['url']?>/info/<?=e($value['url'])?>"<?=((isset($this->url[1]) && $this->url[1] == $value['url'] && $this->url[0] == 'info') ? ' class="menu-active"' : '')?>><?=e($value['title'])?></a></div>
            <?php endforeach ?>

            <!-- Link para painel administrativo (apenas para admins logados) -->
            <?php if(isset($_SESSION['adminUsername'])): ?><div class="footer-element"><a href="<?=$data['url']?>/admin"<?=((isset($this->url[0]) && $this->url[0] == 'admin') ? ' class="menu-active"' : '')?>><?=e($lang['admin'])?></a></div><?php endif ?>
        </div>

        <!-- Informações de copyright e créditos -->
        <div class="footer-info">
            <!-- Créditos do software (apenas desktop) -->
            <div class="footer-element desktop-only"><?=sprintf($lang['powered_by'], '<a href="'.SOFTWARE_URL.'" target="_blank" data-nd>'.SOFTWARE_NAME.'</a>')?></div>
            <!-- Copyright dinâmico -->
            <div class="footer-element"><?=sprintf($lang['copyright'], $data['year'], e($data['settings']['site_title']))?></div>
        </div>
    </div>
</footer>