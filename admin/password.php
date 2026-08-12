<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/admin.php';
require_once __DIR__ . '/../lib/json_store.php';

admin_check_auth();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!session_csrf_check()) {
        $error = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            $error = 'A nova senha e a confirmação não conferem.';
        } else {
            $result = admin_change_password($current, $new);
            if (!empty($result['success'])) {
                set_flash('success', $result['success']);
                header('Location: /admin/dashboard.php');
                exit;
            }
            $error = $result['error'] ?? 'Erro ao alterar senha.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trocar Senha — Painel Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body class="bg-slate-100 min-h-screen font-sans">

  <nav class="bg-slate-800 text-white px-6 py-3 flex items-center justify-between">
    <a href="/admin/dashboard.php" class="font-bold text-lg">← Painel Admin</a>
    <a href="/admin/logout.php" class="text-sm text-red-300 hover:text-red-200">Sair</a>
  </nav>

  <div class="max-w-md mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">🔑 Trocar Senha</h1>
    <p class="text-slate-500 mb-6">Digite sua senha atual e a nova senha.</p>

    <?php if ($error): ?>
      <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        ❌ <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
      <?= admin_csrf_input() ?>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Senha atual</label>
        <input type="password" name="current_password" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
               autocomplete="current-password">
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nova senha</label>
        <input type="password" name="new_password" required minlength="6"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
               autocomplete="new-password">
        <p class="text-xs text-slate-400 mt-1">Mínimo de 6 caracteres.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Confirmar nova senha</label>
        <input type="password" name="confirm_password" required minlength="6"
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
               autocomplete="new-password">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit"
                class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
          🔑 Alterar Senha
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
