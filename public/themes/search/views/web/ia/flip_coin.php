<?php
defined('FIR') OR exit();
/**
 * Template para exibição da Resposta Instantânea do Lançamento de Moeda
 * 
 * Este arquivo define a interface para exibir o resultado do lançamento de moeda:
 * - Título da resposta instantânea
 * - Resultado (cara ou coroa)
 * 
 * Funcionalidades:
 * - Exibe resultado aleatório do lançamento
 * - Layout em card para destaque visual
 * - Integração com sistema de idiomas
 * - Design responsivo
 */
?>
<div class="row row-card-result">
    <div class="web-ia web-ia-ip">
        <!-- Título da resposta instantânea -->
        <div class="web-ia-title"><?=$lang['you_have_flipped']?></div>
        
        <!-- Conteúdo: resultado do lançamento (cara ou coroa) -->
        <div class="web-ia-content"><?=$data['result']?></div>
    </div>
</div>