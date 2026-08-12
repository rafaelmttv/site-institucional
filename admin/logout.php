<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/admin.php';

admin_logout();
header('Location: /admin/login.php');
exit;
