<?php

namespace Fir\Models;

/**
 * Classe Info representa o modelo para páginas de informação do sistema
 */
class Info extends Model {

    /**
     * Busca uma página específica pelo nome
     *
     * @param string $param Nome da página
     */
    public function getPage($param) {}

    /**
     * Retorna todas as páginas de informação disponíveis
     *
     * @return array Lista de páginas de informação
     */
    public function getPages() {
        $query = $this->db->prepare("SELECT * FROM `info_pages` ORDER BY `id`");
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $data = [];
        // Monta array associativo com dados das páginas
        while($row = $result->fetch_assoc()) {
            $data[$row['url']]['title']     = $row['title'];
            $data[$row['url']]['url']       = $row['url'];
            $data[$row['url']]['public']    = $row['public'];
            $data[$row['url']]['content']   = $row['content'];
        }
        return $data;
    }
}