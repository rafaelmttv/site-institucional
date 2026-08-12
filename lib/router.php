<?php
declare(strict_types=1);

/**
 * Roteador simples: array de rotas -> visualização + título.
 * Registra apenas rotas principais do site institucional.
 * Qualquer rota não mapeada cai em home como fallback.
 */

// Rotas: 'url' => ['view' => 'caminho/para/view.php', 'title' => 'Título da Página']
return [
    '/'         => ['view' => 'views/home.php',     'title' => 'Início'],
    '/sobre'    => ['view' => 'views/about.php',    'title' => 'Sobre Nós'],
    '/servicos' => ['view' => 'views/services.php', 'title' => 'Serviços'],
    '/contato'  => ['view' => 'views/contact.php',  'title' => 'Contato'],
    '/admin'    => ['view' => 'admin/index.php',    'title' => 'Painel Admin'],
];

/**
 * Função principal: despacha rota e renderiza view.
 *
 * @param array  $routes        Array de rotas carregado deste arquivo
 * @param array  $config        Configuração completa do site
 * @param string $fallback_view View de fallback (padrão: home)
 */
function dispatch(array $routes, array $config, string $fallback_view = 'views/home.php'): void
{
    // Remover barras iniciais e finais
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = preg_replace('/^\/+|\/+$/', '', $uri);
    $uri = trim($uri, '/');

    // Normalizar: remover query string
    if (false !== strpos($uri, '?')) {
        $uri = substr($uri, 0, strpos($uri, '?'));
    }

    // Decodificar se tiver %20 etc
    $uri = rawurldecode($uri);

    // Garantir barra inicial
    if ($uri === '' || $uri === '/') {
        $uri = '/';
    } else {
        $uri = '/' . $uri;
    }

    // Buscar rota
    $route = $routes[$uri] ?? null;

    if ($route) {
        $view_path  = $route['view'];
        $page_title = $route['title'];
    } else {
        // Fallback: tentar como página content JSON
        $view_path  = $fallback_view;
        $page_title = config('seo.description', 'Início');
    }

    // Adaptar caminho relativo à raiz do projeto
    $project_root = dirname(__DIR__);
    $full_path    = $project_root . '/' . $view_path;

    // Carregar conteúdo JSON da página correspondente
    $page_key       = ltrim($uri, '/');
    $page_key       = $page_key === '' ? 'home' : $page_key;
    // Mapear URLs amigáveis para nomes de arquivo JSON
    $page_key_map = [
        'home'     => 'home',
        'sobre'    => 'about',
        'servicos' => 'services',
        'contato'  => 'contact',
    ];
    $json_key       = $page_key_map[$page_key] ?? $page_key;
    $page_content   = json_store_read($json_key);

    // Gerar CSS variables do tema
    $theme_css = generate_theme_css($config);

    // Meta tags SEO
    $meta_tags = '';

    // Structured data (JSON-LD)
    $structured_data = generate_structured_data($config, $page_content);

    // Plausible Analytics
    $plausible = '';
    if (feature('analytics') && config('analytics.plausible_domain')) {
        $plausible = '<script defer data-domain="' . e(config('analytics.plausible_domain')) .
                     '" src="https://plausible.io/js/script.js"></script>';
    }

    // Flash messages
    $flash = get_flash();

    // Renderizar view dentro do layout
    include $project_root . '/views/layout.php';
}

/**
 * Gera CSS variables do tema a partir da configuração.
 */
function generate_theme_css(array $config): string
{
    $theme = $config['theme'] ?? [];
    $vars  = [];

    foreach (['primary', 'secondary', 'accent', 'dark', 'light'] as $key) {
        if (!empty($theme[$key])) {
            $vars[] = '--color-' . $key . ': ' . $theme[$key] . ';';
        }
    }

    return ':root { ' . implode(' ', $vars) . ' }';
}

/**
 * Gera dados estruturados JSON-LD para SEO.
 */
function generate_structured_data(array $config, array $page_content): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => config('name'),
        'url'      => config('url'),
        'logo'     => config('logo') ? asset(config('logo')) : '',
        'telephone' => config('contact.phone'),
        'email'    => config('contact.email'),
        'address'  => [
            '@type' => 'PostalAddress',
            'streetAddress' => config('contact.address'),
        ],
    ];

    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}
