<?php
defined('FIR') OR exit();
/**
 * Template para exibição das linhas de resultados de busca Web
 * 
 * Este arquivo define a estrutura de cada resultado individual de busca web:
 * - Título do site com link
 * - URL do site
 * - Descrição/snippet do conteúdo
 * - Deep links (links internos do site)
 * 
 * Funcionalidades:
 * - Links configuráveis para abrir em nova aba
 * - Truncamento inteligente de descrições
 * - Deep links organizados em colunas
 * - Suporte a HTML nas descrições
 * - Layout responsivo para diferentes tamanhos de tela
 */
?>
<div class="site-result">
    <!-- Título do site com link -->
    <div class="site-title"><a href="<?=$data['results']['url']?>"<?=($data['cookie']['new_window'] > 0 ? ' target="_blank"' : '')?> data-nd><?=$data['results']['name']?></a></div>
    
    <!-- URL do site -->
    <div class="site-url"><a href="<?=$data['results']['url']?>"<?=($data['cookie']['new_window'] > 0 ? ' target="_blank"' : '')?> data-nd><?=$data['results']['displayUrl']?></a></div>
    
    <!-- Descrição/snippet do conteúdo (limitado a 256 caracteres) -->
    <div class="site-description"><?=truncate($data['results']['snippet'], 256, ['ellipsis' => $lang['ellipsis'], 'html' => true, 'exact' => false])?></div>

    <!-- Deep links (links internos do site) -->
    <?php if(isset($data['results']['deepLinks'])): ?>
        <div class="<?php if(array_key_exists_r('snippet', $data['results']['deepLinks']) == true): ?>deep-links<?php else: ?>deep-links-inline<?php endif ?>">
            <!-- Loop através dos deep links -->
            <?php for($i = 0, $n = count($data['results']['deepLinks']), $t = array_key_exists_r('snippet', $data['results']['deepLinks']); $i < $n; $i++): ?>
                <!-- Início de nova coluna se houver snippets -->
                <?php if($i == 0 || $i == round(($n)/2)): ?>
                    <?php if($t == true): ?>
                        <div class="deep-links-col">
                    <?php endif ?>
                <?php endif ?>
                
                <!-- Link individual -->
                <div class="deep-link">
                    <div class="site-title"><a href="<?=$data['results']['deepLinks'][$i]['url']?>"<?=($data['cookie']['new_window'] > 0 ? ' target="_blank"' : '')?> data-nd><?=$data['results']['deepLinks'][$i]['name']?></a><?php if(isset($data['results']['deepLinks'][$i]['snippet'])): ?><span class="site-description"><?=truncate($data['results']['deepLinks'][$i]['snippet'], 75, ['ellipsis' => $lang['ellipsis'], 'html' => true, 'exact' => false])?></span><?php endif ?></div>
                </div>
                
                <!-- Fim de coluna se houver snippets -->
                <?php if($i == (round($n/2)-1) || $i == ($n-1)): ?>
                    <?php if($t == true): ?>
                        </div>
                    <?php endif ?>
                <?php endif ?>
            <?php endfor ?>
        </div>
    <?php endif ?>
</div>