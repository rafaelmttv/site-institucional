<?php
declare(strict_types=1);

/**
 * Funções utilitárias globais.
 * Carregado uma vez por index.php ou admin.php.
 */

$__config_cache  = null;
$__content_cache = [];

/**
 * Carrega (e faz cache) a configuração do site.
 */
function load_config(): array
{
    global $__config_cache;
    if ($__config_cache === null) {
        $__config_cache = require __DIR__ . '/../config/site.php';
    }
    return $__config_cache;
}

/**
 * Acessa config aninhada por dot notation.
 * Ex: config('theme.primary') => '#2563eb'
 */
function config(string $key, mixed $default = null): mixed
{
    static $cache = null;
    if ($cache === null) $cache = load_config();

    $keys = explode('.', $key);
    $val = $cache;
    foreach ($keys as $k) {
        if (!is_array($val) || !array_key_exists($k, $val)) return $default;
        $val = $val[$k];
    }
    return $val;
}

/**
 * Verifica se uma feature está ativa.
 */
function feature(string $name): bool
{
    return (bool) config('features.' . $name, false);
}

/**
 * Escapa texto para HTML (XSS protection).
 */
function e(mixed $val): string
{
    return htmlspecialchars((string) $val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Gera URL absoluta para asset, com cache-busting por timestamp.
 */
function asset(string $path): string
{
    $full = dirname(__DIR__) . '/' . ltrim($path, '/');
    $v = file_exists($full) ? filemtime($full) : '';
    return '/' . ltrim($path, '/') . ($v ? '?v=' . $v : '');
}

/**
 * Gera link de WhatsApp com mensagem pré-preenchida.
 */
function whatsapp_link(string $message = ''): string
{
    $num = config('contact.whatsapp', '');
    $msg = urlencode($message);
    return "https://wa.me/{$num}?text={$msg}";
}

/**
 * Inicia sessão se ainda não ativa.
 */
function session_start_if(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

/**
 * Gera/obtém token CSRF da sessão.
 */
function session_csrf_token(): string
{
    session_start_if();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica token CSRF.
 */
function session_csrf_check(): bool
{
    session_start_if();
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Gera input hidden com token CSRF para formulários.
 */
function admin_csrf_input(): string
{
    $token = session_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Define mensagem flash.
 */
function set_flash(string $type, string $message): void
{
    session_start_if();
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Obtém e limpa mensagens flash.
 */
function get_flash(): array
{
    session_start_if();
    $flash = [];
    foreach (['success', 'error', 'info'] as $type) {
        if (!empty($_SESSION['flash_' . $type])) {
            $flash[$type] = $_SESSION['flash_' . $type];
            unset($_SESSION['flash_' . $type]);
        }
    }
    return $flash;
}

/**
 * Gera meta tags SEO para o <head>.
 */
function meta_tags(string $title, string $description = '', string $image = ''): string
{
    $site_name = config('name', '');
    $full_title = $title ? "{$title} — {$site_name}" : $site_name;
    $desc = $description ?: config('seo.description', '');
    $img = $image ? asset($image) : asset(config('seo.og_image', ''));

    $html = "<title>{$full_title}</title>\n";
    $html .= "  <meta name=\"description\" content=\"" . e($desc) . "\">\n";
    if ($kw = config('seo.keywords')) {
        $html .= "  <meta name=\"keywords\" content=\"" . e($kw) . "\">\n";
    }
    $html .= "  <meta property=\"og:title\" content=\"" . e($full_title) . "\">\n";
    $html .= "  <meta property=\"og:description\" content=\"" . e($desc) . "\">\n";
    $html .= "  <meta property=\"og:image\" content=\"" . e($img) . "\">\n";
    $html .= "  <meta property=\"og:type\" content=\"website\">\n";
    $html .= "  <meta name=\"twitter:card\" content=\"summary_large_image\">\n";

    return $html;
}

/**
 * Processa formulário de contato (POST).
 * Retorna array com 'success' ou 'error'.
 */
function handle_contact_form(): array
{
    if (($_POST['contact_form'] ?? '') !== '1') return [];

    // Honeypot anti-spam
    if (!empty($_POST['website_hp'])) return [];

    if (!session_csrf_check()) {
        return ['error' => 'Token de segurança inválido. Recarregue a página.'];
    }

    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        return ['error' => 'Preencha todos os campos obrigatórios.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'E-mail inválido.'];
    }

    // Salvar mensagem em arquivo (fallback simples)
    $log_dir = dirname(__DIR__) . '/content/messages';
    if (!is_dir($log_dir)) mkdir($log_dir, 0775, true);

    $entry = sprintf(
        "[%s] %s <%s> %s\n%s\n---\n",
        date('Y-m-d H:i:s'),
        $name, $email, $phone ?: 'sem telefone',
        $message
    );
    file_put_contents($log_dir . '/contact_log.txt', $entry, FILE_APPEND | LOCK_EX);

    // Tentar enviar e-mail (se method=mail)
    if (config('form.method') === 'mail') {
        $to      = config('form.to_email', '');
        $subject = "Contato pelo site — {$name}";
        $body    = "Nome: {$name}\nE-mail: {$email}\nTelefone: {$phone}\n\n{$message}";
        $headers = "From: {$email}\r\nReply-To: {$email}";

        if ($to) {
            @mail($to, $subject, $body, $headers);
        } else {
            // Log de fallback se não houver destinatário
        }
    }

    return ['success' => 'Mensagem enviada com sucesso! Responderemos em breve.'];
}

/**
 * array_replace_deep — merge recursivo de arrays.
 */
function array_replace_deep(array $base, array $override): array
{
    foreach ($override as $key => $value) {
        if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
            $base[$key] = array_replace_deep($base[$key], $value);
        } else {
            $base[$key] = $value;
        }
    }
    return $base;
}
