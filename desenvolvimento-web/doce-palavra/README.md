
# Doce Palavra — MVP (PHP + MySQL)

## Requisitos
- PHP 8+ e MySQL 8+ (XAMPP/Laragon)
- Extensão PDO habilitada

## Instalação
1. Crie o banco:
   - Abra o MySQL e execute `database/setup.sql` (no Workbench ou phpMyAdmin).
2. Configure o acesso ao DB:
   - Edite `src/config/config.php` com host, usuário e senha do seu MySQL.
3. Coloque o projeto em:
   - `C:\xampp\htdocs\doce-palavra` (Windows) ou no `www`/`public_html` equivalente.
4. Acesse no navegador:
   - `http://localhost/doce-palavra/public/login.php`

## Acesso inicial
- **E-mail:** admin@docepalavra.org
- **Senha:** Admin@123
> Troque a senha após o primeiro login.

## Funcionalidades
- Cadastro de livros.
- Registro de sessões de leitura.
- Relatórios básicos por período.

## Próximos passos
- CRUD de Creches e Turmas (tabelas já existem).
- CSRF, validações e auditoria.
- Upload de fotos (evidências) e exportação CSV/PDF.
