<?php

namespace Fir\Models;

/**
 * Classe SearchLimit controla o limite de buscas por IP
 */
class SearchLimit extends Model {

    /**
     * Obtém as informações de busca de um IP
     *
     * @param array $params Parâmetros contendo o IP
     * @return array Dados do IP (ip, count, updated_at)
     */
    public function getIp($params) {
        $query = $this->db->prepare("SELECT * FROM `search_limit` WHERE `ip` = ?");
        $query->bind_param('s', $params['ip']);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $data = [];
        // Monta array com os dados retornados do banco
        while($row = $result->fetch_assoc()) {
            $data['ip']         = $row['ip'];
            $data['count']      = $row['count'];
            $data['updated_at'] = $row['updated_at'];
        }
        return $data;
    }

    /**
     * Adiciona ou atualiza o status do IP do usuário
     *
     * @param array $params Parâmetros contendo o IP e o novo count
     */
    public function addIp($params) {
        $query = $this->db->prepare("INSERT INTO `search_limit` (`ip`, `count`) VALUES(?, 1) ON DUPLICATE KEY UPDATE `ip` = VALUES(`ip`), `count` = ?, `updated_at` = `updated_at`");
        $query->bind_param('si', $params['ip'], $params['count']);
        $query->execute();
        $query->close();
    }

    /**
     * Reseta o contador de buscas do IP do usuário
     *
     * @param array $params Parâmetros contendo o IP
     */
    public function resetIp($params) {
        $query = $this->db->prepare("UPDATE `search_limit` SET `count` = 1 WHERE `ip` = ?");
        $query->bind_param('s', $params['ip']);
        $query->execute();
        $query->close();
    }
}