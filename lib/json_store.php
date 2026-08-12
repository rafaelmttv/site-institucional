<?php
declare(strict_types=1);

/**
 * JSON Store — persistência de conteúdo em arquivos JSON.
 * Substitui banco de dados. Lê e escreve em content/*.json.
 */

function json_store_path(string $page): string
{
    return dirname(__DIR__) . '/content/' . basename($page) . '.json';
}

function json_store_read(string $page): array
{
    $path = json_store_path($page);
    if (!file_exists($path)) return [];
    $raw  = file_get_contents($path);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_store_write(string $page, array $data): bool
{
    $path = json_store_path($page);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json) !== false;
}

function json_store_list_pages(): array
{
    $dir   = dirname(__DIR__) . '/content';
    $pages = [];
    foreach (glob($dir . '/*.json') as $file) {
        $name = basename($file, '.json');
        if ($name === 'settings') continue; // settings não é página
        $data = json_decode(file_get_contents($file), true);
        $pages[] = [
            'key'   => $name,
            'label' => $data['_label'] ?? ucfirst($name),
            'file'  => basename($file),
            'size'  => filesize($file),
        ];
    }
    return $pages;
}

/**
 * Lista páginas editáveis no admin (metadados).
 */
function admin_page_list(): array
{
    return [
        ['key' => 'home',     'label' => 'Início (Home)',  'icon' => 'house'],
        ['key' => 'about',     'label' => 'Sobre Nós',       'icon' => 'info'],
        ['key' => 'services',  'label' => 'Serviços',        'icon' => 'briefcase'],
        ['key' => 'contact',   'label' => 'Contato',         'icon' => 'envelope'],
    ];
}

/**
 * Configurações editáveis pelo admin (salvas em content/settings.json).
 * Estas sobrescrevem valores de config/site.php em tempo de execução.
 */
function json_store_read_settings(): array
{
    $path = dirname(__DIR__) . '/content/settings.json';
    if (!file_exists($path)) return [];
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function json_store_write_settings(array $data): bool
{
    $path = dirname(__DIR__) . '/content/settings.json';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json) !== false;
}
