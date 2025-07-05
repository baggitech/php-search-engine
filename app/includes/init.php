<?php
/**
 * @author Lázaro Baggi (Baggitech) - baggitech@gmail.com
 * @copyright Lázaro Baggi (Baggitech) - Brasil. All rights reserved.
 * @version 1.0
 * @link https://baggitech.com.br
 */

// Define o caminho dos cookies da sessão conforme configuração do sistema
session_set_cookie_params(null, COOKIE_PATH);
// Inicia a sessão PHP
session_start();

// Carrega as classes principais do núcleo do sistema
require_once(__DIR__ . '/../core/App.php');
require_once(__DIR__ . '/../core/Middleware.php');
require_once(__DIR__ . '/../core/Controller.php');
require_once(__DIR__ . '/../core/Model.php');
require_once(__DIR__ . '/../core/View.php');
require_once(__DIR__ . '/../core/Database.php');
require_once(__DIR__ . '/../core/Language.php');