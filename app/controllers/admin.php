<?php

namespace Fir\Controllers;

use Fir\Libraries\Search;
use GuzzleHttp\Client;

// Controller responsável pelo painel administrativo do sistema
// Gerencia login, dashboard, configurações, aparência, anúncios, temas, idiomas, páginas institucionais e logout

class Admin extends Controller {

    /**
     * @var object
     */
    protected $admin;

    /**
     * Redireciona para a página de login do admin.
     * Rota padrão do painel administrativo.
     */
    public function index() {
        redirect('admin/login');
    }

    /**
     * Exibe e processa o formulário de login do admin.
     * - Instancia o modelo Admin e monta o menu.
     * - Se o formulário foi enviado, define usuário e senha.
     * - Se "lembrar-me" foi marcado, gera token persistente.
     * - Tenta autenticar.
     * - Se sucesso, redireciona para dashboard.
     * - Se falha, exibe mensagem de erro e faz logout.
     * - Renderiza a view de login.
     */
    public function login() {
        $this->admin = $this->model('Admin');
        $data['menu_view'] = $this->menu();

        // Se o usuário tentou fazer login
        if(isset($_POST['login'])) {
            $this->admin->username = $data['username'] = $_POST['username'];
            $this->admin->password = $_POST['password'];

            // Se o usuário marcou "lembrar-me"
            if(isset($_POST['remember'])) {
                $this->setToken(); // Gera token de sessão persistente
            }
        }

        // Tenta autenticar o usuário
        $auth = $this->auth();

        // Se autenticado, redireciona para o dashboard
        if($auth) {
            redirect('admin/dashboard');
        }
        // Se falhou, exibe mensagem de erro
        elseif(isset($_POST['login'])) {
            $_SESSION['message'][] = ['error', $this->lang['invalid_user_pass']];
            $this->logout(false);
        }

        $data['settings_view'] = $this->view->render($data, 'admin/login');
        $data['page_title'] = $this->lang['login'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['login']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe o painel principal do admin.
     * Monta o menu, define título e renderiza a view do dashboard.
     */
    public function dashboard() {
        $data['menu_view'] = $this->menu();
        $data['settings_view'] = $this->view->render($data, 'admin/dashboard');
        $data['page_title'] = $this->lang['dashboard'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['dashboard']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa as configurações gerais do site.
     * - Instancia modelo Admin e monta menu.
     * - Se formulário enviado, valida timezone e salva configurações.
     * - Busca configurações salvas.
     * - Renderiza a view de configurações gerais.
     */
    public function general() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva as configurações se o formulário foi enviado
        if(isset($_POST['submit'])) {
            // Valida o timezone
            $_POST['timezone'] = (in_array($_POST['timezone'], timezone_identifiers_list()) ? $_POST['timezone'] : '');

            // Se não houver erros, salva as configurações
            if(empty($_SESSION['message'])) {
                $this->admin->general($_POST);
                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }
            redirect('admin/general');
        }

        // Busca as configurações salvas
        $data['site_settings'] = $this->admin->getSiteSettings();
        $data['settings_view'] = $this->view->render($data, 'admin/general');
        $data['page_title'] = $this->lang['general'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['general']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa as configurações de busca.
     * - Instancia modelo Admin e Search, monta menu.
     * - Se formulário enviado, valida e ajusta campos.
     * - Salva configurações.
     * - Busca configurações salvas e mercados.
     * - Renderiza a view de configurações de busca.
     */
    public function search() {
        $search = new Search();
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva as configurações se o formulário foi enviado
        if(isset($_POST['submit'])) {
            // Validação básica dos campos
            $_POST['search_per_ip'] = (int)$_POST['search_per_ip'] >= 0 ? (int)$_POST['search_per_ip'] : 0;
            $_POST['suggestions_per_ip'] = (int)$_POST['suggestions_per_ip'] >= 0 ? (int)$_POST['suggestions_per_ip'] : 0;
            $_POST['search_answers'] = ($_POST['search_answers'] > 0 ? 1 : 0);
            $_POST['search_suggestions'] = ($_POST['search_suggestions'] > 0 ? 1 : 0);
            $_POST['search_related'] = ($_POST['search_related'] > 0 ? 1 : 0);
            $_POST['web_per_page'] = $_POST['web_per_page'] > 49 || $_POST['web_per_page'] < 0 ? 49 : (int)$_POST['web_per_page'];
            $_POST['images_per_page'] = $_POST['images_per_page'] > 149 || $_POST['images_per_page'] < 0 ? 149 : (int)$_POST['images_per_page'];
            $_POST['videos_per_page'] = $_POST['videos_per_page'] > 104 || $_POST['videos_per_page'] < 0 ? 104 : (int)$_POST['videos_per_page'];
            $_POST['news_per_page'] = $_POST['news_per_page'] > 49 || $_POST['news_per_page'] < 0 ? 49 : (int)$_POST['news_per_page'];
            $_POST['search_new_window'] = ($_POST['search_new_window'] > 0 ? 1 : 0);
            $_POST['search_entities'] = (int)$_POST['search_entities'] >= 0 ? (int)$_POST['search_entities'] : 0;
            $_POST['search_privacy'] = ($_POST['search_privacy'] > 0 ? 1 : 0);
            // Se todos os campos de resultados estiverem zerados, define padrão
            if($_POST['web_per_page'] == 0 && $_POST['images_per_page'] == 0 && $_POST['videos_per_page'] == 0 && $_POST['news_per_page']) {
                $_POST['web_per_page'] = 20;
            }

            if($_POST['search_per_ip'] == 0) {
                $this->admin->deleteSearchLimit();
            }

            // Se não houver erros, salva as configurações
            if(empty($_SESSION['message'])) {
                $this->admin->search($_POST);
                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }
            redirect('admin/search');
        }

        // Busca as configurações salvas
        $data['site_settings'] = $this->admin->getSiteSettings();
        $data['markets'] = $search->getMarkets();
        $data['settings_view'] = $this->view->render($data, 'admin/search');
        $data['page_title'] = $this->lang['search'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['search']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa as configurações de aparência do site.
     * - Instancia modelo Admin e monta menu.
     * - Se formulário enviado, valida campos e processa uploads.
     * - Salva configurações.
     * - Busca configurações salvas.
     * - Renderiza a view de aparência.
     */
    public function appearance() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva as configurações se o formulário foi enviado
        if(isset($_POST['submit'])) {
            $_POST['site_backgrounds'] = (int)$_POST['site_backgrounds'] >= 0 ? (int)$_POST['site_backgrounds'] : 0;
            $_POST['site_dark_mode'] = (int)$_POST['site_dark_mode'] >= 0 ? (int)$_POST['site_dark_mode'] : 0;
            $_POST['site_center_content'] = (int)$_POST['site_center_content'] >= 0 ? (int)$_POST['site_center_content'] : 0;

            $fields = ['logo_small', 'logo_small_dark', 'logo_large', 'logo_large_dark', 'favicon'];
            foreach($_FILES as $key => $value) {
                // Valida os campos de upload de imagem
                if(in_array($key, $fields)) {
                    if(!empty($_FILES[$key]['name'])) {
                        $fileFormat = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                        // Se não houver erro e for PNG ou SVG
                        if($_FILES[$key]['error'] == 0 && in_array($fileFormat, ['png', 'svg'])) {
                            $fileName = $key.'.'.$fileFormat;
                            $path = sprintf('%s/../../%s/%s/brand/', __DIR__, PUBLIC_PATH, UPLOADS_PATH);
                            if(move_uploaded_file($_FILES[$key]['tmp_name'], $path.$fileName) == false) {
                                $_SESSION['message'][] = ['error', sprintf($this->lang['upload_error_code'], $_FILES[$key]['error'])];
                            } else {
                                // Remove imagem antiga se necessário
                                $oldFileName = $this->settings[$key] ?? null;
                                if($oldFileName && $oldFileName != $fileName) {
                                    unlink($path.$oldFileName);
                                }
                                $this->admin->insertUpdate($key, $fileName);
                            }
                        } else {
                            $_SESSION['message'][] = ['error', sprintf($this->lang['upload_error_code'], $_FILES[$key]['error'])];
                        }
                    }
                }
            }

            // Se não houver erros, salva as configurações
            if(empty($_SESSION['message'])) {
                $this->admin->appearance($_POST);
                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }

            redirect('admin/appearance');
        }

        // Get the newly saved settings
        $data['site_settings'] = $this->admin->getSiteSettings();

        $data['settings_view'] = $this->view->render($data, 'admin/appearance');
        $data['page_title'] = $this->lang['appearance'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['appearance']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa as configurações de anúncios do site.
     * - Instancia modelo Admin e monta menu.
     * - Se formulário enviado, valida campos e salva configurações.
     * - Busca configurações salvas.
     * - Renderiza a view de anúncios.
     */
    public function ads() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva as configurações se o formulário foi enviado
        if(isset($_POST['submit'])) {
            // Validação dos campos de anúncios
            $_POST['ads_safe'] = (int)$_POST['ads_safe'] >= 0 ? (int)$_POST['ads_safe'] : 0;
            // Se não houver erros, salva as configurações
            if(empty($_SESSION['message'])) {
                $this->admin->ads($_POST);
                $_SESSION['message'][] = ['success', $this->lang['settings_saved']];
            }
            redirect('admin/ads');
        }

        // Busca as configurações salvas
        $data['site_settings'] = $this->admin->getSiteSettings();
        $data['settings_view'] = $this->view->render($data, 'admin/ads');
        $data['page_title'] = $this->lang['ads'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['ads']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa a alteração de senha do admin.
     * Valida senha atual, nova senha e confirmação, atualiza no banco e exibe mensagens de retorno.
     */
    public function password() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva a nova senha se o formulário foi enviado
        if(isset($_POST['submit'])) {
            // Validação da senha
            if(strlen($_POST['password']) < 6) {
                $_SESSION['message'][] = ['error', $this->lang['password_short']];
            } elseif($_POST['password'] !== $_POST['password_confirm']) {
                $_SESSION['message'][] = ['error', $this->lang['password_mismatch']];
            } else {
                // Atualiza a senha
                $this->admin->password(['password' => password_hash($_POST['password'], PASSWORD_BCRYPT)]);
                $_SESSION['message'][] = ['success', $this->lang['password_changed']];
            }
            redirect('admin/password');
        }

        $data['settings_view'] = $this->view->render($data, 'admin/password');
        $data['page_title'] = $this->lang['password'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['password']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa a seleção de temas do site.
     * Busca temas disponíveis, permite seleção e atualização do tema ativo.
     */
    public function themes() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva o tema selecionado se o formulário foi enviado
        if(isset($_POST['submit'])) {
            // Validação do tema
            if(!empty($_POST['theme'])) {
                $this->admin->setTheme(['theme' => $_POST['theme']]);
                $_SESSION['message'][] = ['success', $this->lang['theme_changed']];
            }
            redirect('admin/themes');
        }

        // Busca o tema atual e os disponíveis
        $data['site_settings'] = $this->admin->getSiteSettings();
        $data['themes'] = $this->getThemes();
        $data['settings_view'] = $this->view->render($data, 'admin/themes');
        $data['page_title'] = $this->lang['themes'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['themes']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e processa a seleção de idiomas do site.
     * Busca idiomas disponíveis, permite seleção e atualização do idioma ativo.
     */
    public function languages() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva o idioma selecionado se o formulário foi enviado
        if(isset($_POST['submit'])) {
            if(!empty($_POST['language'])) {
                $this->admin->setLanguage(['language' => $_POST['language']]);
                $_SESSION['message'][] = ['success', $this->lang['language_changed']];
            }
            redirect('admin/languages');
        }

        // Busca o idioma atual e os disponíveis
        $data['site_settings'] = $this->admin->getSiteSettings();
        $data['languages'] = $this->getLanguages();
        $data['settings_view'] = $this->view->render($data, 'admin/languages');
        $data['page_title'] = $this->lang['languages'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['languages']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Exibe e gerencia páginas institucionais (criação, edição, exclusão).
     * Permite listar, adicionar, editar e excluir páginas institucionais do site.
     */
    public function info_pages() {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];
        $data['menu_view'] = $this->menu();

        // Salva ou edita páginas institucionais se o formulário foi enviado
        if(isset($_POST['submit'])) {
            // Validação dos campos da página
            if(empty($_POST['title']) || empty($_POST['url'])) {
                $_SESSION['message'][] = ['error', $this->lang['fields_required']];
            } else {
                $this->admin->addInfoPage($_POST);
                $_SESSION['message'][] = ['success', $this->lang['info_page_saved']];
            }
            redirect('admin/info_pages');
        }

        // Busca as páginas institucionais
        $data['info_pages'] = $this->admin->getInfoPages();
        $data['settings_view'] = $this->view->render($data, 'admin/info_pages');
        $data['page_title'] = $this->lang['info_pages'];
        $this->view->metadata['title'] = [$this->lang['admin'], $this->lang['info_pages']];
        return ['content' => $this->view->render($data, 'admin/content')];
    }

    /**
     * Valida dados de uma página institucional antes de salvar.
     * Verifica campos obrigatórios, unicidade de URL e outros requisitos.
     * @param array $page Dados da página
     * @param int $type Tipo de validação (criação ou edição)
     */
    private function validateInfoPage($page, $type) {
        $this->admin = $this->model('Admin');
        $this->admin->username = $_SESSION['adminUsername'];

        if($type) {
            $checkPage = $this->admin->getInfoPage($_POST['page_url'], 1);
            // Define o ID da página para atualização
            $_POST['page_id'] = $page['id'];
        }

        // Limita e sanitiza os campos
        $_POST['page_title'] = substr(strip_tags($_POST['page_title']), 0, 64);
        $_POST['page_url'] = filter_var(substr(htmlspecialchars(strip_tags($_POST['page_url'])), 0, 64), FILTER_SANITIZE_URL);
        $_POST['page_public'] = ($_POST['page_public'] == 1 ? 1 : 0);

        // Verifica se algum campo obrigatório está vazio
        if(empty($_POST['page_title']) || empty($_POST['page_url']) || empty($_POST['page_content'])) {
            $_SESSION['message'][] = ['error', $this->lang['all_fields_required']];
        }

        // Verifica se já existe uma página com a mesma URL
        if($type) {
            if($_POST['page_url'] == $checkPage['url'] && $this->url[3] != $checkPage['id']) {
                $_SESSION['message'][] = ['error', sprintf($this->lang['page_url_exists'], $_POST['page_url'])];
            }
        } else {
            if(isset($page['url'])) {
                $_SESSION['message'][] = ['error', sprintf($this->lang['page_url_exists'], $_POST['page_url'])];
            }
        }
    }

    /**
     * Encerra a sessão do admin e redireciona para login, se necessário.
     * @param bool $redirect Se true, redireciona após logout
     */
    public function logout($redirect = true) {
        unset($_SESSION['adminUsername']);
        session_destroy();
        if($redirect) {
            redirect('admin/login');
        }
    }

    /**
     * Monta o menu lateral do painel admin.
     * Retorna HTML do menu para as views administrativas.
     */
    private function menu() {
        // Define os itens do menu conforme o status do admin
        if(isset($_SESSION['isAdmin'])) {
            $data['menu'] = [
                'dashboard'     => [false, false],
                'general'       => [false, false],
                'search'        => [false, false],
                'appearance'    => [false, false],
                'themes'        => [false, false],
                'languages'     => [false, false],
                'info_pages'    => [false, false],
                'ads'           => [false, false],
                'password'      => [false, false],
                'logout'        => [false, true]
            ];
        } else {
            $data['menu'] = [
                'login'         => [false, false],
            ];
        }

        // Destaca o item do menu da rota atual
        if (array_key_exists($this->url[1], $data['menu'])) {
            $data['menu'][$this->url[1]][0] = true;
        }

        return $this->view->render($data, 'admin/menu');
    }

    /**
     * Busca todos os idiomas disponíveis para o painel admin.
     * Retorna array de idiomas encontrados.
     */
    private function getLanguages() {
        $path = sprintf('%s/../languages/', __DIR__, PUBLIC_PATH, THEME_PATH);
        $languages = scandir($path);
        $output = [];
        foreach($languages as $language) {
            // Seleciona apenas arquivos .php
            if($language != '.' && $language != '..' && substr($language, -4, 4) == '.php') {
                $language = substr($language, 0, -4);
                // Carrega informações do idioma
                require($path.$language.'.php');
                $output[$language]['name'] = $name;
                $output[$language]['author'] = $author;
                $output[$language]['url'] = $url;
                $output[$language]['path'] = $language;
            }
        }
        return $output;
    }

    /**
     * Busca todos os temas disponíveis para o painel admin.
     * Retorna array de temas encontrados.
     */
    private function getThemes() {
        $path = sprintf('%s/../../%s/%s/', __DIR__, PUBLIC_PATH, THEME_PATH);
        $themes = scandir($path);
        $output = [];
        foreach($themes as $theme) {
            // Verifica se o tema possui info.php e ícone
            if(file_exists($path.$theme.'/info.php') && file_exists($path.$theme.'/icon.png')) {
                // Carrega informações do tema
                require($path.$theme.'/info.php');
                $output[$theme]['name']     = $name;
                $output[$theme]['author']   = $author;
                $output[$theme]['url']      = $url;
                $output[$theme]['version']  = $version;
                $output[$theme]['path']     = $theme;
            }
        }
        return $output;
    }

    /**
     * Realiza a autenticação do admin (login).
     * Verifica usuário e senha/token, retorna true se autenticado.
     */
    private function auth() {
        // Se já está autenticado na sessão
        if(isset($_SESSION['adminUsername']) && isset($_SESSION['adminPassword'])) {
            $this->admin->username = $_SESSION['adminUsername'];
            $this->admin->password = $_SESSION['adminPassword'];
            $auth = $this->admin->get(1);
            if($this->admin->password == $auth['password']) {
                $logged = true;
            }
        }
        // Se está autenticado via cookie (lembrar-me)
        elseif(isset($_COOKIE['adminUsername']) && isset($_COOKIE['adminToken'])) {
            $this->admin->username = $_COOKIE['adminUsername'];
            $this->admin->rememberToken = $_COOKIE['adminToken'];
            $auth = $this->admin->get(2);
            if($this->admin->rememberToken == $auth['remember_token'] && !empty($auth['remember_token'])) {
                $_SESSION['adminUsername'] = $this->admin->username;
                $this->setPassword($auth['password']);
                $logged = true;
            }
        }
        // Se está tentando autenticar agora
        else {
            $auth = $this->admin->get(0);
            // Seta as sessões
            $_SESSION['adminUsername'] = $this->admin->username;
            $this->setPassword($this->admin->password);
            if(isset($auth['password']) && password_verify($this->admin->password, $auth['password'])) {
                if($this->admin->rememberToken) {
                    $this->admin->renewToken();
                }
                session_regenerate_id();
                $logged = true;
            }
        }
        if(isset($logged)) {
            $_SESSION['isAdmin'] = true;
            return $auth;
        }
        return false;
    }

    /**
     * Define a senha do admin (criptografia, se aplicável).
     * @param string $password Nova senha
     */
    private function setPassword($password) {
        $_SESSION['adminPassword'] = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Gera e define o token de "lembrar-me" para autenticação persistente.
     */
    private function setToken() {
        $this->admin->rememberToken = password_hash($this->admin->username.generateSalt().time().generateSalt(), PASSWORD_DEFAULT);
        setcookie("adminUsername", $this->admin->username, time() + 30 * 24 * 60 * 60, COOKIE_PATH, null, 1);
        setcookie("adminToken", $this->admin->rememberToken, time() + 30 * 24 * 60 * 60, COOKIE_PATH, null, 1);
        $_SESSION['adminRemember'] = true;
    }
}