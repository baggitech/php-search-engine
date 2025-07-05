<?php
defined('FIR') OR exit();
/**
 * Template para exibição da Resposta Instantânea do IP do Usuário
 * 
 * Este arquivo define a interface para exibir o endereço IP do usuário:
 * - Título da resposta instantânea
 * - Endereço IP atual do usuário
 * 
 * Funcionalidades:
 * - Exibe o IP real do usuário
 * - Layout em card para destaque visual
 * - Integração com sistema de idiomas
 * - Design responsivo
 */
?>
<div class="row row-card-result">
    <div class="web-ia web-ia-user-ip">
        <!-- Título da resposta instantânea -->
        <div class="web-ia-title"><?=$lang['your_ip_is']?></div>
        
        <!-- Conteúdo: endereço IP do usuário -->
        <div class="web-ia-content"><?=$data['result']?></div>
    </div>
</div>