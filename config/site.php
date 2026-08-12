<?php
declare(strict_types=1);

/**
 * ============================================================
 * CONFIGURAÇÃO DO SITE — Edite este arquivo por cliente.
 * ============================================================
 * Este é o ÚNICO arquivo PHP que precisa ser editado para
 * customizar o site para um novo cliente. O conteúdo das páginas
 * fica em content/*.json (editável pelo painel admin).
 * ============================================================
 */

return [
    'name'    => 'Empresa Modelo',
    'tagline' => 'Soluções que transformam seu negócio',
    'logo'    => 'assets/img/logo.png',
    'favicon' => 'assets/img/favicon.ico',
    'lang'    => 'pt-BR',
    'url'     => 'http://localhost:8000',

    // Cores do tema (injetadas como CSS variables no <head>)
    'theme' => [
        'primary'   => '#2563eb',
        'secondary' => '#0ea5e9',
        'accent'    => '#f59e0b',
        'dark'      => '#1e293b',
        'light'     => '#f8fafc',
    ],

    // Informações de contato
    'contact' => [
        'phone'       => '+55 11 9999-9999',
        'whatsapp'    => '5511999999999',
        'email'       => 'contato@empresa.com.br',
        'address'     => 'Rua Exemplo, 123 — São Paulo/SP',
        'hours'       => 'Seg-Sex: 8h às 18h',
        'maps_embed'  => '',
    ],

    // Redes sociais (null = não exibir)
    'social' => [
        'facebook'  => 'https://facebook.com/empresa',
        'instagram' => 'https://instagram.com/empresa',
        'linkedin'  => null,
        'youtube'   => null,
    ],

    // Menu de navegação
    'nav' => [
        ['label' => 'Início',    'url' => '/'],
        ['label' => 'Sobre',     'url' => '/sobre'],
        ['label' => 'Serviços',  'url' => '/servicos'],
        ['label' => 'Contato',   'url' => '/contato'],
    ],

    // SEO global
    'seo' => [
        'description'        => 'Empresa modelo — soluções para seu negócio',
        'keywords'            => 'empresa, serviço, cidade',
        'og_image'            => 'assets/img/og-image.jpg',
        'google_site_verify'  => null,
    ],

    // Funcionalidades on/off
    'features' => [
        'whatsapp_button' => true,
        'blog'             => false,
        'gallery'          => true,
        'testimonials'     => false,
        'faq'              => true,
        'google_maps'      => false,
        'newsletter'       => false,
    ],

    // Configuração do formulário de contato
    'form' => [
        'method'       => 'mail',     // 'mail' ou 'api'
        'api_endpoint' => null,       // URL da API se method = 'api'
        'to_email'     => 'contato@empresa.com.br',
    ],

    // Analytics (Plausible — leve, privacy-first)
    'plausible' => [
        'enabled'      => false,
        'domain'       => 'seusite.com',
        'data_domain'  => 'seusite.com',
        'avoid_cookie' => true,
        'src'          => null,       // null = não carregar; URL externa se desejado
    ],

    // Credenciais do painel admin
    // ⚠️ Altere a senha antes de publicar!
    'admin' => [
        'username' => 'admin',
        'password' => 'admin123',     // Texto plano para setup inicial. Use password_hash() para produção.
    ],
];
