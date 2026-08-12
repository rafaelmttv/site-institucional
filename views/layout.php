<?php
/** Layout base — <head>, header, content, footer. */
/** @var string $view_path  Caminho da view a incluir */
/** @var string $page_title Título da página */
/** @var array  $page_content Conteúdo JSON da página */
/** @var array  $flash        Mensagens flash */
/** @var string $theme_css    CSS variables do tema */
/** @var string $plausible    Script Plausible (se ativo) */
/** @var string $structured_data JSON-LD */
?>
<!DOCTYPE html>
<html lang="<?php echo e(config('lang', 'pt-BR')); ?>" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php echo meta_tags($page_title ?? '', $page_content['hero']['subtitle'] ?? '', $page_content['hero']['image'] ?? ''); ?>

  <link rel="icon" href="<?php echo asset(config('favicon', 'assets/img/favicon.ico')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo asset('assets/css/app.css'); ?>">

  <!-- CSS variables do tema (cores dinâmicas) -->
  <style><?php echo $theme_css; ?></style>

  <?php echo $structured_data; ?>

  <?php if ($gsv = config('seo.google_site_verify')): ?>
    <meta name="google-site-verification" content="<?php echo e($gsv); ?>">
  <?php endif; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
  <script src="<?php echo asset('assets/js/alpine.min.js'); ?>" defer></script>
  <script src="<?php echo asset('assets/js/app.js'); ?>"></script>

  <?php include __DIR__ . '/partials/header.php'; ?>

  <main>
    <?php include $view_path; ?>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php if (feature('whatsapp_button')) include __DIR__ . '/partials/whatsapp.php'; ?>

  <?php echo $plausible; ?>
</body>
</html>
