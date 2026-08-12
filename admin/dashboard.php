<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/admin.php';
require_once __DIR__ . '/../lib/json_store.php';

admin_check_auth();

$pages = admin_page_list();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin — Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body class="bg-slate-100 min-h-screen font-sans">

  <!-- NAVBAR -->
  <nav class="bg-slate-800 text-white px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <span class="font-bold text-lg">Painel Admin</span>
      <span class="text-sm text-slate-400">— <?= e(config('name')) ?></span>
    </div>
    <div class="flex items-center gap-4">
      <a href="/" target="_blank" class="text-sm text-slate-300 hover:text-white">Ver site ↗</a>
      <a href="/admin/settings.php" class="text-sm text-slate-300 hover:text-white">Configurações</a>
      <a href="/admin/password.php" class="text-sm text-slate-300 hover:text-white">Trocar Senha</a>
      <a href="/admin/logout.php" class="text-sm text-red-300 hover:text-red-200">Sair</a>
    </div>
  </nav>

  <div class="max-w-4xl mx-auto px-6 py-8">

    <?php if (!empty($flash['success'])): ?>
      <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
        ✅ <?= e($flash['success']) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($flash['error'])): ?>
      <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
        ❌ <?= e($flash['error']) ?>
      </div>
    <?php endif; ?>

    <h1 class="text-2xl font-bold text-slate-800 mb-2">Páginas do Site</h1>
    <p class="text-slate-500 mb-8">Clique em uma página para editar seu conteúdo.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <?php foreach ($pages as $p): ?>
        <a href="/admin/edit.php?page=<?= e($p['key']) ?>"
           class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md hover:border-blue-300 transition">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-2xl">
              <?php
                $icons = ['house' => '🏠', 'info' => 'ℹ️', 'briefcase' => '💼', 'envelope' => '✉️'];
                echo $icons[$p['icon']] ?? '📄';
              ?>
            </div>
            <div>
              <h3 class="font-bold text-slate-800"><?= e($p['label']) ?></h3>
              <p class="text-sm text-slate-500">content/<?= e($p['key']) ?>.json</p>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap gap-3">
      <a href="/admin/settings.php"
         class="inline-block px-4 py-2 rounded-lg bg-slate-700 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ⚙️ Editar Configurações
      </a>
      <a href="/admin/password.php"
         class="inline-block px-4 py-2 rounded-lg bg-slate-600 text-white text-sm font-semibold hover:bg-slate-700 transition">
        🔑 Trocar Senha
      </a>
    </div>
  </div>

</body>
</html>
