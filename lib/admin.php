<?php
declare(strict_types=1);

/**
 * Admin Auth — login/logout simples via sessão PHP.
 * Credenciais vindas de config/site.php (array 'admin').
 * Zero banco de dados.
 */

function admin_check_auth(): void
{
    session_start_if();
    if (empty($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Obtém a senha atual do admin.
 * Prioridade: settings.json (se tiver hash) > config/site.php.
 */
function admin_get_password(): string
{
    $settings = json_store_read_settings();
    if (!empty($settings['admin_password'])) {
        return $settings['admin_password'];
    }
    return config('admin.password', 'admin123');
}

/**
 * Obtém o usuário atual do admin.
 * Prioridade: settings.json > config/site.php.
 */
function admin_get_username(): string
{
    $settings = json_store_read_settings();
    if (!empty($settings['admin_username'])) {
        return $settings['admin_username'];
    }
    return config('admin.username', 'admin');
}

function admin_login(string $username, string $password): bool
{
    $cfg_user = admin_get_username();
    $cfg_pass = admin_get_password();

    // Se a senha do config NÃO é hash bcrypt, comparamos em texto plano
    // (para setup inicial fácil). Se for hash, usamos password_verify().
    if (str_starts_with($cfg_pass, '$2y$')) {
        $ok = ($username === $cfg_user) && password_verify($password, $cfg_pass);
    } else {
        $ok = ($username === $cfg_user) && ($password === $cfg_pass);
    }

    if ($ok) {
        session_start_if();
        session_regenerate_id(true);
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user']   = $username;
    }
    return $ok;
}

function admin_logout(): void
{
    session_start_if();
    $_SESSION = [];
    session_destroy();
}

/**
 * Altera a senha do admin.
 * Salva como hash bcrypt em content/settings.json.
 */
function admin_change_password(string $current, string $new): array
{
    $cfg_pass = admin_get_password();

    // Verificar senha atual
    if (str_starts_with($cfg_pass, '$2y$')) {
        $valid = password_verify($current, $cfg_pass);
    } else {
        $valid = ($current === $cfg_pass);
    }

    if (!$valid) {
        return ['error' => 'A senha atual está incorreta.'];
    }

    if (strlen($new) < 6) {
        return ['error' => 'A nova senha deve ter pelo menos 6 caracteres.'];
    }

    // Gerar hash bcrypt da nova senha
    $hash = password_hash($new, PASSWORD_DEFAULT);

    // Salvar no settings.json (mescla com settings existentes)
    $settings = json_store_read_settings();
    $settings['admin_password'] = $hash;

    if (json_store_write_settings($settings)) {
        return ['success' => 'Senha alterada com sucesso!'];
    }

    return ['error' => 'Erro ao salvar a nova senha.'];
}
