<?php
declare(strict_types=1);

/**
 * Front Controller — ponto de entrada único do site.
 * Carrega config, helpers, router e despacha a rota.
 */

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/json_store.php';

// Carregar configuração (base + overrides de settings.json, se existir)
$config = array_replace_deep(load_config(), json_store_read_settings());

// Rotas: router.php define dispatch() E retorna o array de rotas
$routes = require_once __DIR__ . '/lib/router.php';

// Despachar
dispatch($routes, $config, 'views/home.php');
