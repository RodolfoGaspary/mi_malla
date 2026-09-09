# Mi Malla — quoting tool

A quoting tool built for a safety-netting installer. Built around the client record
rather than the quote, because most clients come back.

PHP · MySQL · PDO · jQuery · Bootstrap 5

## The problem

A house that starts with the balcony often ends up enclosing the stairwell and the
roof terrace too. Every return visit meant asking again for name, phone, email and
address. The data existed — scattered across notebooks and chat threads — but nowhere
it could be retrieved from.

## How it works

Starting a quote begins with a search by first or last name; results appear as you
type, and picking one fills the form. New clients are saved from the same screen
without leaving the flow. Clients are matched on full name **or** phone number, so a
second visit updates the existing record instead of creating a duplicate.

An installation has no fixed number of panels, so the areas section starts with one
row — type, location, width, height — and rows are added as needed. Square metres are
calculated per row and totalled server-side on save, inside a transaction that writes
client, header and detail together or writes nothing.

## Layout

```
Pages/
  login.php               Sign in
  dashboard.php           Landing screen
  proforma.php            Quote builder
  register.php            Create user (admin only)
  cambiar_password.php    Change own password
  includes/
    sesion.php            Session guard — required by every page and endpoint
    csrf.php              Per-session CSRF token helpers
    db.php                PDO wrapper over Queries/db_connect.php
    admin_check.php       Role check
    search_user.php       Client autocomplete (AJAX)
    get_user.php          Client detail (AJAX)
    guardar_cliente.php   Create/update client (AJAX, JSON)
    guardar_proforma.php  Quote save — transactional
    header.php            Nav
    logout.php            Sign out
Queries/db_connect.php    Connection (not in the repo — provide your own)
schema.sql                Tables the quote save expects
```

## Setup

```bash
git clone https://github.com/RodolfoGaspary/mi_malla.git
# create Queries/db_connect.php defining $pdo
mysql -u root your_database < schema.sql
```

Then create the first admin directly in the database:

```sql
UPDATE users SET role = 'admin' WHERE username = 'your_user';
```

`login.php` falls back to `'user'` and the username when `role` or `nombre` are
missing, so existing accounts keep working, but the admin menu needs the role set.

## Security

- Session required on every page **and** every AJAX endpoint; endpoints return
  `401 JSON` rather than redirecting to the login HTML
- CSRF token verified on every POST
- Passwords hashed with bcrypt via `password_hash` / `password_verify`
- Session ID regenerated on login and on password change
- All queries use prepared statements with bound parameters
- Database errors are logged, never echoed to the browser

## Known gaps

- No rate limiting on login attempts
- `proformas.php` and `instalaciones.php` are linked in the nav but not built
- Credentials live in `Queries/db_connect.php` — keep it out of version control
