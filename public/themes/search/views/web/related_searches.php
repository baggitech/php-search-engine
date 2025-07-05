<?php
defined('FIR') OR exit();
/**
 * Template para exibição dos resultados de Buscas Relacionadas
 * 
 * Este arquivo define a interface para exibir buscas relacionadas à consulta atual:
 * - Título da seção
 * - Lista de buscas relacionadas
 * - Organização em colunas para melhor visualização
 * 
 * Funcionalidades:
 * - Destaca a consulta original nos resultados relacionados
 * - Organiza links em duas colunas para melhor layout
 * - Links diretos para novas buscas
 * - Escape automático para segurança
 * - Destaque visual da consulta original
 */
?>
<div class="related-searches">
    <!-- Título da seção de buscas relacionadas -->
    <div class="related-title">
        <?=$lang['related_searches']?>
    </div>
    
    <!-- Container dos links relacionados -->
    <div class="related-links">
        <!-- Loop através das buscas relacionadas -->
        <?php for($i = 0, $n = count($data['results']); $i < $n; $i++): ?>
            <!-- Início de nova coluna (primeira ou metade) -->
            <?php if($i == 0 || $i == round(($n)/2)): ?>
                <div class="related-col">
            <?php endif ?>
            
            <!-- Link individual de busca relacionada -->
            <div>
                <a href="<?=$data['url']?>/web?q=<?=e($data['results'][$i]['text'])?>"><?=preg_replace('/'.preg_quote($data['query'], '/').'/ui', '<span class="related-query">'.e($data['query']).'</span>', $data['results'][$i]['displayText'], 1)?></a>
            </div>
            
            <!-- Fim de coluna (meio ou final) -->
            <?php if($i == (round($n/2)-1) || $i == ($n-1)): ?>
                </div>
            <?php endif ?>
        <?php endfor ?>
    </div>
</div>