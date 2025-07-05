<?php
defined('FIR') OR exit();
/**
 * Template para exibição da página de erro "Nenhum Resultado Encontrado"
 * 
 * Este arquivo define a interface quando uma busca não retorna resultados:
 * - Mensagem principal de erro
 * - Consulta que não retornou resultados
 * - Sugestões para melhorar a busca
 * 
 * Funcionalidades:
 * - Exibe a consulta que falhou
 * - Lista de sugestões para refinar a busca
 * - Integração com sistema de mensagens
 * - Escape automático da consulta para segurança
 * - Layout responsivo para diferentes dispositivos
 */
?>
<div class="row row-no-results">
    <!-- Exibe mensagens do sistema (erros, avisos, etc.) -->
    <?=$this->message()?>
    
    <!-- Container principal do erro -->
    <div class="no-results">
        <!-- Mensagem principal com a consulta que falhou -->
        <?=sprintf($this->lang['no_results_found'], '<strong>'.e($_GET['q']).'</strong>')?>
        
        <!-- Título das sugestões -->
        <p><?=$lang['suggestions']?></p>
        
        <!-- Lista de sugestões para melhorar a busca -->
        <ul>
            <li><?=$lang['suggestion_1']?></li>
            <li><?=$lang['suggestion_2']?></li>
            <li><?=$lang['suggestion_3']?></li>
        </ul>
    </div>
</div>