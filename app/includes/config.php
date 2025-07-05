<?php
/**
 * @author Lázaro Baggi (Baggitech) - baggitech@gmail.com
 * @copyright Lázaro Baggi (Baggitech) - Brasil. All rights reserved.
 * @version 1.0
 * @link https://baggitech.com.br
 */
// Desativa a exibição de erros (pode ser alterado para desenvolvimento)
error_reporting(0);

// Define constante para controle interno do sistema
define('FIR', true);

// Configurações do banco de dados
// Host do banco de dados
define('DB_HOST', 'localhost');
// Usuário do banco de dados
define('DB_USER', 'root');
// Nome do banco de dados
define('DB_NAME', 'phpsearchengine_db');
// Senha do banco de dados
define('DB_PASS', '');

// Caminho base do sistema (ajuste para ambiente de produção)
// define('URL_PATH', 'https://example.com');
define('URL_PATH', 'http://localhost/phpsearchengine.dev');

// Diretórios principais do sistema
// Pasta pública (onde ficam os arquivos acessíveis via web)
define('PUBLIC_PATH', 'public');
// Pasta de temas
define('THEME_PATH', 'themes');
// Pasta de armazenamento
define('STORAGE_PATH', 'storage');
// Pasta de uploads
define('UPLOADS_PATH', 'uploads');

// Define o caminho dos cookies baseado na URL do sistema
define('COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', URL_PATH).'/');