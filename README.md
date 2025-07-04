# phpSearchEngine

Um motor de busca web desenvolvido em PHP, baseado em arquitetura MVC própria, com integração à API do Bing para fornecer resultados de busca (web, imagens, vídeos, notícias). O sistema permite personalização visual, preferências do usuário e possui um painel administrativo completo.

## Funcionalidades Principais
- Busca web, imagens, vídeos e notícias via API do Bing
- Preferências de usuário (tema, idioma, filtros de busca) sem necessidade de login
- Painel administrativo para configuração do sistema, aparência, anúncios, temas, idiomas e páginas institucionais
- Internacionalização (multi-idioma)
- Estrutura modular e fácil de manter

## Estrutura do Projeto
- `app/` — Núcleo da aplicação (controllers, models, libraries, helpers, middleware, core)
- `public/` — Arquivos públicos (index.php, assets, uploads, views)
- `Documentation/` — Documentação, scripts de atualização e banco de dados
- `vendor/` — Dependências externas (não subir para o GitHub)

## Requisitos
- PHP >= 7.4
- MySQL/MariaDB
- Composer

## Instalação
1. Clone o repositório:
   ```sh
   git clone https://github.com/seuusuario/phpsearchengine.git
   ```
2. Instale as dependências:
   ```sh
   composer install
   ```
3. Configure o banco de dados:
   - Importe o arquivo `Documentation/MySQL/Database.sql` em seu MySQL.
   - Renomeie `app/includes/config.example.php` para `config.php` e preencha com suas credenciais.
4. Configure a chave da API do Bing no painel admin ou diretamente no banco de dados.
5. Acesse `public/index.php` pelo navegador.

## Segurança
- **NUNCA** suba arquivos com senhas, chaves de API ou dados sensíveis para o repositório.
- Use o arquivo `.gitignore` para evitar subir `vendor/`, arquivos de configuração e uploads.
- Troque todas as senhas e chaves após o upload para o GitHub.

## Migração para Laravel
Este projeto será migrado para o framework Laravel. Recomendações:
- Crie uma branch separada para a migração (`laravel-migration`).
- Utilize o relatório de avaliação inicial (`Relatorio_Avaliacao_Inicial.md`) para mapear funcionalidades e rotas.
- Implemente autenticação, rotas, controllers e views seguindo as melhores práticas do Laravel.

## Licença
Defina a licença do projeto conforme sua necessidade (MIT, GPL, etc).

---

**Dúvidas ou sugestões?** Abra uma issue ou entre em contato! 