<?php
declare(strict_types=1);

/**
 * Rate limiting para login admin.
 * Persiste tentativas por IP em arquivo JSON com file locking.
 * Limita a 5 tentativas por minuto; após exceder, bloqueia por 5 minutos.
 */

const RATE_LIMIT_MAX_ATTEMPTS   = 5;
const RATE_LIMIT_WINDOW_SECONDS = 60;
const RATE_LIMIT_LOCKOUT_SECONDS = 300;

function rate_limit_file(): string
{
    return dirname(__DIR__) . '/content/.login_attempts.json';
}

function rate_limit_get_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function rate_limit_read(): array
{
    $path = rate_limit_file();
    if (!file_exists($path)) return [];
    $fp = fopen($path, 'r');
    if (!$fp) return [];
    flock($fp, LOCK_SH);
    $raw = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function rate_limit_write(array $data): void
{
    $path = rate_limit_file();
    $fp = fopen($path, 'c+');
    if (!$fp) return;
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * Verifica se o IP pode tentar login.
 * Retorna ['blocked' => bool, 'remaining' => int, 'retry_after' => int]
 */
function rate_limit_check(): array
{
    $ip = rate_limit_get_client_ip();
    $data = rate_limit_read();
    $now = time();

    $entry = $data[$ip] ?? null;

    if ($entry && ($entry['locked_until'] ?? 0) > $now) {
        return [
            'blocked'     => true,
            'remaining'   => 0,
            'retry_after' => $entry['locked_until'] - $now,
        ];
    }

    $count = 0;
    if ($entry) {
        $count = count(array_filter(
            $entry['attempts'] ?? [],
            fn($t) => $t > $now - RATE_LIMIT_WINDOW_SECONDS
        ));
    }

    return [
        'blocked'     => false,
        'remaining'   => max(0, RATE_LIMIT_MAX_ATTEMPTS - $count),
        'retry_after' => 0,
    ];
}

/**
 * Registra uma tentativa falha de login.
 */
function rate_limit_record_failure(): void
{
    $ip = rate_limit_get_client_ip();
    $data = rate_limit_read();
    $now = time();

    if (!isset($data[$ip])) {
        $data[$ip] = ['attempts' => [], 'locked_until' => 0];
    }

    // Remove tentativas fora da janela de tempo
    $data[$ip]['attempts'] = array_values(array_filter(
        $data[$ip]['attempts'] ?? [],
        fn($t) => $t > $now - RATE_LIMIT_WINDOW_SECONDS
    ));

    $data[$ip]['attempts'][] = $now;

    // Bloqueia se excedeu o limite
    if (count($data[$ip]['attempts']) >= RATE_LIMIT_MAX_ATTEMPTS) {
        $data[$ip]['locked_until'] = $now + RATE_LIMIT_LOCKOUT_SECONDS;
    }

    rate_limit_write($data);
}

/**
 * Limpa tentativas após login bem-sucedido.
 */
function rate_limit_clear(): void
{
    $ip = rate_limit_get_client_ip();
    $data = rate_limit_read();
    unset($data[$ip]);
    rate_limit_write($data);
}

/**
 * Remove IPs expirados (housekeeping).
 */
function rate_limit_cleanup(): void
{
    $data = rate_limit_read();
    $now = time();
    $changed = false;

    foreach ($data as $ip => $entry) {
        $expired = ($entry['locked_until'] ?? 0) < $now
            && empty(array_filter(
                $entry['attempts'] ?? [],
                fn($t) => $t > $now - RATE_LIMIT_WINDOW_SECONDS
            ));
        if ($expired) {
            unset($data[$ip]);
            $changed = true;
        }
    }

    if ($changed) {
        rate_limit_write($data);
    }
}
