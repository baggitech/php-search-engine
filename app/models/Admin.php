<?php

namespace Fir\Models;

/**
 * Classe Admin representa o modelo de administração do sistema
 */
class Admin extends Model {

    /**
     * Nome de usuário do administrador
     * @var string
     */
    public $username;

    /**
     * Senha do administrador
     * @var string
     */
    public $password;

    /**
     * Token de "lembrar-me" para autenticação persistente
     * @var string
     */
    public $rememberToken;

    /**
     * Seleciona um administrador conforme o tipo de consulta
     *
     * @param int $type Tipo de consulta: 2 (por token), 1 (por senha), null (por usuário)
     * @return array Dados do administrador encontrado
     */
    public function get($type = null) {
        if($type == 2) {
            // Busca por usuário e token de "lembrar-me"
            $query = $this->db->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `remember_token` = ?");
            $query->bind_param('ss', $this->username, $this->rememberToken);
        } elseif($type == 1) {
            // Busca por usuário e senha
            $query = $this->db->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?");
            $query->bind_param('ss', $this->username, $this->password);
        } else {
            // Busca apenas por usuário
            $query = $this->db->prepare("SELECT * FROM `admin` WHERE `username` = ?");
            $query->bind_param('s', $this->username);
        }
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        $query->close();
        return $result;
    }

    /**
     * Insere ou atualiza uma configuração pelo nome
     *
     * @param string $name  Nome da configuração
     * @param string $value Valor da configuração
     */
    public function insertUpdate($name, $value) {
        $query = $this->db->prepare("INSERT INTO `settings` (`name`, `value`) VALUES(?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`)");
        $query->bind_param('ss', $name, $value);
        $query->execute();
        $query->close();
    }

    /**
     * Salva as configurações gerais do painel admin
     *
     * @param array $params Parâmetros das configurações gerais
     */
    public function general($params) {
        $query = $this->db->prepare("INSERT INTO `settings` (`name`, `value`) VALUES('site_title', ?), ('site_tagline', ?), ('timezone', ?), ('cookie_policy_url', ?), ('tracking_code', ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`)");
        $query->bind_param('sssss', $params['site_title'], $params['site_tagline'], $params['timezone'], $params['cookie_policy_url'], $params['tracking_code']);
        $query->execute();
        $query->close();
    }

    /**
     * Salva as configurações de busca do painel admin
     *
     * @param array $params Parâmetros das configurações de busca
     */
    public function search($params) {
        $query = $this->db->prepare("INSERT INTO `settings` (`name`, `value`) VALUES('search_api_key', ?), ('web_per_page', ?), ('images_per_page', ?), ('videos_per_page', ?), ('news_per_page', ?), ('search_sites', ?), ('search_market', ?), ('search_per_ip', ?), ('suggestions_per_ip', ?), ('search_answers', ?), ('search_suggestions', ?), ('search_related', ?), ('search_new_window', ?), ('search_safe_search', ?), ('search_highlight', ?), ('search_entities', ?), ('search_privacy', ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`)");
        $query->bind_param('sssssssssssssssss', $params['search_api_key'], $params['web_per_page'], $params['images_per_page'], $params['videos_per_page'], $params['news_per_page'], $params['search_sites'], $params['search_market'], $params['search_per_ip'], $params['suggestions_per_ip'], $params['search_answers'], $params['search_suggestions'], $params['search_related'], $params['search_new_window'], $params['search_safe_search'], $params['search_highlight'], $params['search_entities'], $params['search_privacy']);
        $query->execute();
        $query->close();
    }

    /**
     * Salva as configurações de aparência do painel admin
     *
     * @param array $params Parâmetros das configurações de aparência
     */
    public function appearance($params) {
        $query = $this->db->prepare("INSERT INTO `settings` (`name`, `value`) VALUES('site_backgrounds', ?), ('site_dark_mode', ?), ('site_center_content', ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`)");
        $query->bind_param('sss', $params['site_backgrounds'], $params['site_dark_mode'], $params['site_center_content']);
        $query->execute();
        $query->close();
    }

    /**
     * Salva as configurações de anúncios do painel admin
     *
     * @param array $params Parâmetros das configurações de anúncios
     */
    public function ads($params) {
        $query = $this->db->prepare("INSERT INTO `settings` (`name`, `value`) VALUES('ads_safe', ?), ('ads_1', ?), ('ads_2', ?), ('ads_3', ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `value` = VALUES(`value`)");
        $query->bind_param('isss', $params['ads_safe'], $params['ads_1'], $params['ads_2'], $params['ads_3']);
        $query->execute();
        $query->close();
    }

    /**
     * Salva a senha do administrador
     *
     * @param array $params Parâmetros contendo a nova senha
     */
    public function password($params) {
        $query = $this->db->prepare("UPDATE `admin` SET `password` = ? WHERE `username` = ?");
        $query->bind_param('ss', $params['password'], $this->username);
        $query->execute();
        $query->close();
    }

    /**
     * Renova o token de "lembrar-me" do administrador
     */
    public function renewToken() {
        $query = $this->db->prepare("UPDATE `admin` SET `remember_token` = ? WHERE `username` = ?");
        $query->bind_param('ss', $this->rememberToken, $this->username);
        $query->execute();
        $query->close();
    }

    /**
     * Define o tema do site
     *
     * @param array $params Parâmetros contendo o tema
     */
    public function setTheme($params) {
        $query = $this->db->prepare("UPDATE `settings` SET `value` = ? WHERE `name` = 'site_theme'");
        $query->bind_param('s', $params['theme']);
        $query->execute();
        $query->close();
    }

    /**
     * Define o idioma padrão do site
     *
     * @param array $params Parâmetros contendo o idioma
     */
    public function setLanguage($params) {
        $query = $this->db->prepare("UPDATE `settings` SET `value` = ? WHERE `name` = 'site_language'");
        $query->bind_param('s', $params['language']);
        $query->execute();
        $query->close();
    }

    /**
     * Retorna todas as páginas de informação disponíveis
     *
     * @return array Lista de páginas de informação
     */
    public function getInfoPages() {
        $query = $this->db->prepare("SELECT * FROM `info_pages` ORDER BY `id` DESC");
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $data = [];
        while($row = $result->fetch_assoc()) {
            $data[$row['id']]['id']        = $row['id'];
            $data[$row['id']]['title']     = $row['title'];
            $data[$row['id']]['url']       = $row['url'];
            $data[$row['id']]['public']    = $row['public'];
            $data[$row['id']]['content']   = $row['content'];
        }
        return $data;
    }

    /**
     * Busca uma página de informação específica
     *
     * @param string $value Valor a ser buscado (id ou url)
     * @param int $type Tipo de busca: 0 (por id), 1 (por url)
     * @return array Dados da página encontrada
     */
    public function getInfoPage($value, $type) {
        if($type) {
            $query = $this->db->prepare("SELECT * FROM `info_pages` WHERE `url` = ?");
        } else {
            $query = $this->db->prepare("SELECT * FROM `info_pages` WHERE `id` = ?");
        }
        $query->bind_param('s', $value);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        $query->close();
        return $result;
    }

    /**
     * Atualiza uma página de informação específica
     *
     * @param array $params Parâmetros da página a ser atualizada
     */
    public function updateInfoPage($params) {
        $query = $this->db->prepare("UPDATE `info_pages` SET `title` = ?, `url` = ?, `public` = ?, `content` = ? WHERE `id` = ?");
        $query->bind_param('ssiss', $params['page_title'], $params['page_url'], $params['page_public'], $params['page_content'], $params['page_id']);
        $query->execute();
        $query->close();
    }

    /**
     * Add a new Info Page
     *
     * @param   array   $params
     */
    public function addInfoPage($params) {
        $query = $this->db->prepare("INSERT INTO `info_pages` (`id`, `title`, `url`, `public`, `content`) VALUES (NULL, ?, ?, ?, ?);");
        $query->bind_param('ssis', $params['page_title'], $params['page_url'], $params['page_public'], $params['page_content']);
        $query->execute();
        $query->close();
    }

    /**
     * Delete an Info Page
     *
     * @param   array   $params
     */
    public function deleteInfoPage($params) {
        $query = $this->db->prepare("DELETE FROM `info_pages` WHERE `id` = ?");
        $query->bind_param('s', $params['page_id']);
        $query->execute();
        $query->close();
    }

    /**
     * Delete all the Search Limits
     */
    public function deleteSearchLimit() {
        $query = $this->db->prepare("TRUNCATE TABLE `search_limit`");
        $query->execute();
        $query->close();
    }
}