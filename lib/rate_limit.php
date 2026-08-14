<?php
declare(strict_types=1);

/**
 * Rate limiting para login admin.
 * Persiste tentativas por arquivo JSON com file locking.
 * Limita 5 tentativas por minuto; após exceder, bloqueia por 5 minutos.
 */
const RATE_LIMIT_MAX_ATTEMPTS = 5;
const RATE_LIMIT_WINDOW_SECONDS = 60;
const RATE_LIMIT_LOCKOUT_SECONDS = 300;

function rate_limit_file(): string {
    return dirname(__DIR__) . '/content/.login_attempts.json';
}

function rate_limit_get_client_ip(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function rate_limit_read(): array {
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

function rate_limit_write(array $data): void {
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

function rate_limit_check(): array {
    $ip = rate_limit_get_client_ip();
    $data = rate_limit_read();
    $now = time();

    // Check if locked until a future time
    if (isset($data[$ip]['locked_until']) && $data[$ip]['locked_until'] > $now) {
        return [
            'blocked' => true,
            'remaining' => 0,
            'retry_after' => $data[$ip]['locked_until'] - $now
        ];
    }

    // Count attempts for this IP in the current window
    $count = 0;
    if (isset($data[$ip])) {
        $count = count($data[$ip]['attempts']);
    }

    // Check if we've exceeded the max attempts
    if ($count >= RATE_LIMIT_MAX_ATTEMPTS) {
        return [
            'blocked' => true,
            'remaining' => 0,
            'retry_after' => RATE_LIMIT_LOCKOUT_SECONDS
        ];
    }

    // Calculate remaining attempts and retry time
    $remaining = RATE_LIMIT_MAX_ATTEMPTS - $count;
    $retry_after = 0;

    if ($count > 0) {
        $last_attempt = end($data[$ip]['attempts']);
        $elapsed = $now - $last_attempt;
        if ($last_attempt > 0 && $elapsed < RATE_LIMIT_WINDOW_SECONDS) {
            $retry_after = RATE_LIMIT_WINDOW_SECONDS - $elapsed;
        }
    }

    return [
        'blocked' => false,
        'remaining' => $remaining,
        'retry_after' => $retry_after
    ];
}