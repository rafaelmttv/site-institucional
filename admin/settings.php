<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/admin.php';
require_once __DIR__ . '/../lib/json_store.php';

admin_check_auth();

$saved_settings = json_store_read_settings();
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!session_csrf_check()) {
        $error = 'Token de segurança inválido.';
    } else {
        $new_settings = [
            'name'    => $_POST['name']    ?? config('name'),
            'tagline' => $_POST['tagline'] ?? config('tagline'),
            'theme'   => [
                'primary'   => $_POST['theme_primary']   ?? config('theme.primary'),
                'secondary' => $_POST['theme_secondary'] ?? config('theme.secondary'),
                'accent'    => $_POST['theme_accent']    ?? config('theme.accent'),
                'dark'      => $_POST['theme_dark']      ?? config('theme.dark'),
                'light'     => $_POST['theme_light']     ?? config('theme.light'),
            ],
            'contact' => [
                'phone'    => $_POST['contact_phone']    ?? '',
                'whatsapp' => $_POST['contact_whatsapp'] ?? '',
                'email'    => $_POST['contact_email']    ?? '',
                'address'  => $_POST['contact_address']  ?? '',
                'hours'    => $_POST['contact_hours']    ?? '',
            ],
        ];

        // Preservar senha do admin se já foi alterada antes
        if (!empty($saved_settings['admin_password'])) {
            $new_settings['admin_password'] = $saved_settings['admin_password'];
        }

        if (json_store_write_settings($new_settings)) {
            set_flash('success', 'Configurações salvas com sucesso!');
            header('Location: /admin/dashboard.php');
            exit;
        } else {
            $error = 'Erro ao salvar configurações.';
        }
    }
}

$current = array_replace_deep(load_config(), $saved_settings);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Configurações — Painel Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body class="bg-slate-100 min-h-screen font-sans">

  <nav class="bg-slate-800 text-white px-6 py-3 flex items-center justify-between">
    <a href="/admin/dashboard.php" class="font-bold text-lg">← Painel Admin</a>
    <a href="/" target="_blank" class="text-sm text-slate-300 hover:text-white">Ver site ↗</a>
  </nav>

  <div class="max-w-2xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Configurações do Site</h1>

    <?php if ($error): ?>
      <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        ❌ <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6 bg-white rounded-xl shadow-sm p-6">
      <?= admin_csrf_input() ?>

      <!-- IDENTIDADE -->
      <div>
        <h2 class="font-bold text-slate-800 border-b pb-2 mb-3">Identidade</h2>
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nome da empresa</label>
            <input type="text" name="name" value="<?= e($current['name'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Slogan / Tagline</label>
            <input type="text" name="tagline" value="<?= e($current['tagline'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
      </div>

      <!-- CORES -->
      <div>
        <h2 class="font-bold text-slate-800 border-b pb-2 mb-3">Cores do Tema</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
          <?php foreach (['primary' => 'Primária', 'secondary' => 'Secundária', 'accent' => 'Destaque', 'dark' => 'Escura', 'light' => 'Clara'] as $key => $label): ?>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1"><?= $label ?></label>
            <div class="flex gap-2">
              <input type="color" id="theme_<?= $key ?>" value="<?= e($current['theme'][$key] ?? '#000000') ?>"
                     onchange="document.getElementById('theme_<?= $key ?>_text').value = this.value"
                     class="w-12 h-10 rounded cursor-pointer border border-slate-300">
              <input type="text" id="theme_<?= $key ?>_text" name="theme_<?= $key ?>"
                     value="<?= e($current['theme'][$key] ?? '') ?>"
                     onchange="document.getElementById('theme_<?= $key ?>').value = this.value"
                     class="flex-1 px-2 py-2 border border-slate-300 rounded-lg text-sm font-mono">
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- CONTATO -->
      <div>
        <h2 class="font-bold text-slate-800 border-b pb-2 mb-3">Contato</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Telefone</label>
            <input type="text" name="contact_phone" value="<?= e($current['contact']['phone'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp (só números)</label>
            <input type="text" name="contact_whatsapp" value="<?= e($current['contact']['whatsapp'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label>
            <input type="email" name="contact_email" value="<?= e($current['contact']['email'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Horário</label>
            <input type="text" name="contact_hours" value="<?= e($current['contact']['hours'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Endereço</label>
            <input type="text" name="contact_address" value="<?= e($current['contact']['address'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-4">
        <button type="submit"
                class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
          💾 Salvar Configurações
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
