# Hotsite Evento - Projeto para XAMPP (PostgreSQL)

Estrutura mínima de um hotsite responsivo com frontend Bootstrap + Leaflet e backend em PHP (PDO) para PostgreSQL.

**Importante**: Este projeto é um **skeleton** funcional com validações básicas, upload de imagens (GD), autenticação de admins, CRUD de eventos/ads, inscrições com lista de espera, export CSV e suporte multilingue (PT-BR / EN).

### Como usar (resumo rápido)
1. Coloque a pasta `hotsite_project` dentro de `htdocs/` do seu XAMPP (ou ajuste Apache DocumentRoot).
2. Configure o PostgreSQL local (porta 5432). Crie um banco `hotsite` (ou altere `config.php`).
3. Importe os scripts SQL em `sql/create_tables.sql` e `sql/seed.sql` (psql ou pgAdmin).
4. Habilite a extensão PgSQL no PHP (php_pgsql). No XAMPP Windows, edite `php.ini` e descomente `extension=pgsql` e `extension=pdo_pgsql` se necessário.
5. Ajuste `config.php` com credenciais do banco e SMTP.
6. Se quiser envio de email funcional, instale o PHPMailer via Composer e ajuste `vendor/phpmailer` conforme README. O projeto inclui um wrapper simples que tenta usar `mail()` se PHPMailer não estiver disponível.
7. Extra: coloque o Bootstrap local em `vendor/bootstrap/` se desejar (os arquivos atuais apontam para CDN mas o projeto aceita offline substituição).

### Arquivos principais
- `index.php` - página pública com mapa e busca de eventos.
- `php/` - lógica do backend (db, auth, uploads, registro de inscrições).
- `admin/` - painel administrativo (registro/login, dashboard, CRUD).
- `sql/create_tables.sql` e `sql/seed.sql` - estruturas e seeds.
- `assets/` - css, js, imagens e placeholders.
- `vendor/` - leaflet scripts estão apontados para CDN mas podem ser trocados para offline.

---
Se algo falhar ao rodar localmente, envie o log/erro que eu te ajudo a ajustar.

