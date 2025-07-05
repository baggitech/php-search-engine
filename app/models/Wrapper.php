<?php

namespace Fir\Models;

/**
 * Classe Wrapper fornece métodos para obter páginas de informação públicas
 */
class Wrapper extends Model {

    /**
     * Retorna as páginas de informação públicas
     *
     * @return array Lista de páginas públicas
     */
    public function getInfoPages() {
        $query = $this->db->prepare("SELECT * FROM `info_pages` WHERE `public` = 1 ORDER BY `id`");
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $data = [];
        // Monta array associativo com título e url das páginas públicas
        while($row = $result->fetch_assoc()) {
            $data[$row['url']]['title']     = $row['title'];
            $data[$row['url']]['url']       = $row['url'];
        }
        return $data;
    }
}