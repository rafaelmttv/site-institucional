<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/admin.php';
require_once __DIR__ . '/../lib/json_store.php';

admin_check_auth();

$page_key = $_GET['page'] ?? '';
if (!$page_key) {
    header('Location: /admin/dashboard.php');
    exit;
}

$pages = admin_page_list();
$valid_keys = array_column($pages, 'key');
if (!in_array($page_key, $valid_keys, true)) {
    header('Location: /admin/dashboard.php');
    exit;
}

$data = json_store_read($page_key);
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!session_csrf_check()) {
        $error = 'Token de segurança inválido.';
    } else {
        $raw_json = $_POST['content_json'] ?? '';
        $decoded  = json_decode($raw_json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'JSON inválido: ' . json_last_error_msg();
        } else {
            if (json_store_write($page_key, $decoded)) {
                set_flash('success', 'Página atualizada com sucesso!');
                header('Location: /admin/dashboard.php');
                exit;
            } else {
                $error = 'Erro ao salvar o arquivo.';
            }
        }
    }
}

$page_label = $pages[array_search($page_key, $valid_keys)]['label'] ?? ucfirst($page_key);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar — <?= e($page_label) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body class="bg-slate-100 min-h-screen font-sans">

  <nav class="bg-slate-800 text-white px-6 py-3 flex items-center justify-between">
    <a href="/admin/dashboard.php" class="font-bold text-lg">← Painel Admin</a>
    <a href="<?= '/' . ($page_key === 'home' ? '' : $page_key) ?>" target="_blank"
       class="text-sm text-slate-300 hover:text-white">Ver página ↗</a>
  </nav>

  <div class="max-w-4xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Editar: <?= e($page_label) ?></h1>

    <?php if ($error): ?>
      <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        ❌ <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <?= admin_csrf_input() ?>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">
          Conteúdo (JSON) — <span class="text-slate-400">formato do arquivo content/<?= e($page_key) ?>.json</span>
        </label>
        <textarea name="content_json" rows="28"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg font-mono text-sm bg-slate-50 focus:ring-2 focus:ring-blue-500"
                  spellcheck="false"><?= e(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?></textarea>
      </div>

      <div class="flex gap-3">
        <button type="submit"
                class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
          💾 Salvar
        </button>
        <a href="/admin/dashboard.php"
           class="px-6 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-semibold hover:bg-slate-300 transition">
          Cancelar
        </a>
      </div>
    </form>
  </div>

</body>
</html>