<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/admin.php';
require_once __DIR__ . '/../lib/json_store.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!session_csrf_check()) {
        $error = 'Token CSRF inválido. Recarregue a página.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (admin_login($username, $password)) {
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: /admin/dashboard.php');
            exit;
        }
        $error = 'Usuário ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin — Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center font-sans">

  <div class="w-full max-w-sm bg-white rounded-xl shadow-lg p-8">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-slate-800">Painel Admin</h1>
      <p class="text-sm text-slate-500 mt-1">Acesse para gerenciar o conteúdo do site</p>
    </div>

    <?php if ($error): ?>
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
        ❌ <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
           <?= admin_csrf_input() ?>
           <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Usuário</label>
        <input type="text" name="username" required autofocus
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
        <input type="password" name="password" required
               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      </div>
      <button type="submit"
              class="w-full py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
        Entrar
      </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">
      <a href="/" class="hover:text-slate-600">← Voltar para o site</a>
    </p>
  </div>

</body>
</html>
