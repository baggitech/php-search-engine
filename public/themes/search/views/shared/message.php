<?php
defined('FIR') OR exit();
/**
 * Template para exibição de mensagens de sucesso, informação e erro
 * 
 * Este arquivo define a estrutura de notificações do sistema:
 * - Mensagens de sucesso (verde)
 * - Mensagens de informação (azul)
 * - Mensagens de erro (vermelho)
 * 
 * Funcionalidades:
 * - Exibe mensagens com estilo baseado no tipo
 * - Botão de fechar para cada tipo de mensagem
 * - Escape automático do conteúdo para segurança
 * - Classes CSS dinâmicas baseadas no tipo de mensagem
 */
?>
<div class="notification-box notification-box-<?=$data['message']['type']?>">
    <!-- Conteúdo da mensagem -->
    <p><?=e($data['message']['content'])?></p>
    
    <!-- Botão de fechar a notificação -->
    <div class="notification-close notification-close-<?=$data['message']['type']?>"></div>
</div>