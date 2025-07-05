<?php
defined('FIR') OR exit();
/**
 * Template para exibição do campo de token CSRF
 * 
 * Este arquivo define um campo hidden para proteção CSRF:
 * - Campo oculto com token único
 * - Proteção contra ataques Cross-Site Request Forgery
 * - Validação de segurança em formulários
 * 
 * Funcionalidades:
 * - Gera token único para cada sessão
 * - Validação automática em submissões de formulário
 * - Proteção contra ataques de falsificação de requisições
 * - Campo oculto para não interferir na interface
 */
?>
<input type="hidden" name="token_id" value="<?=$data['token_id']?>">