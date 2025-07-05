<?php

namespace Fir\Connection;

/**
 * Classe responsável pela conexão e manipulação do banco de dados MySQL
 * Utiliza mysqli para executar queries, preparar statements e gerenciar transações
 */
class Database {

    /**
     * Starts the database connection
     * @return \mysqli
     */
    public function connect() {
        // Cria uma nova conexão mysqli com os dados do config.php
        $db = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        // Verifica se houve erro na conexão
        if($db->connect_errno) {
            echo "Falha ao conectar ao MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
            exit;
        }
        // Define o charset da conexão para UTF-8
        $db->set_charset("utf8");
        return $db;
    }
}