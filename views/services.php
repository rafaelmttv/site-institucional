<?php
/** Página Serviços. */
$c    = $page_content;
$hero = $c['hero'] ?? [];
include __DIR__ . '/partials/hero.php';

foreach ($c['sections'] ?? [] as $section) {
    include __DIR__ . '/partials/section.php';
}

include __DIR__ . '/partials/cta.php';
?>
