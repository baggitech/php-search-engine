<?php

namespace Fir\Models;

/**
 * Classe base para todos os models do sistema
 * Fornece acesso ao banco de dados e métodos utilitários para manipulação de dados
 */
class Model {

    /**
     * The database connection
     * @var	\mysqli
     */
    protected $db;

    function __construct($db) {
        // Armazena a conexão com o banco de dados
        $this->db = $db;
    }

    /**
     * Gets the site `settings`
     *
     * @return	array
     */
    public function getSiteSettings() {
        // Prepara e executa a query para buscar todas as configurações do site
        $query = $this->db->prepare("SELECT * FROM `settings`");
        $query->execute();
        $result = $query->get_result();
        $query->close();

        $data = [];
        // Monta um array associativo com nome => valor das configurações
        while($row = $result->fetch_assoc()) {
            $data[$row['name']] = $row['value'];
        }
        return $data;
    }

    /**
     * @param $string
     * @return string
     */
    private function e($string) {
        // Escapa strings para uso seguro em queries (proteção contra SQL Injection)
        return $this->db->real_escape_string($string);
    }
}