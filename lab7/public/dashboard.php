<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/session.php';
requireLogin();

$allowedThemes = ['light', 'dark'];
$theme = $_COOKIE['theme'] ?? 'light';
if (!is_string($theme) || !in_array($theme, $allowedThemes, true)) {
    $theme = 'light';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    $themeValue = $_POST['theme'] ?? null;
    $token = $_POST['csrf_token'] ?? null;
    if (
        is_string($themeValue) && in_array($themeValue, $allowedThemes, true) &&
        is_string($token) && hash_equals($_SESSION['csrf_token'], $token)
    ) {
        setcookie('theme', $themeValue, [
            'expires' => time() + 2592000,
            'path' => '/',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Location: /dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Журнал</title>
    <style>
        body { background: <?= $theme === 'dark' ? '#333' : '#fff' ?>; color: <?= $theme === 'dark' ? '#fff' : '#000' ?>; }
    </style>
</head>
<body>
    <h1>Электронный журнал</h1>
    <p>Пользователь: <?= h((string)$_SESSION['email']) ?></p>
    <p>Роль: <?= h((string)$_SESSION['role']) ?></p>

    <?php if ($_SESSION['role'] === 'teacher'): ?>
        <div style="border: 2px solid green; padding: 10px; margin: 10px 0;">
            <strong>Панель преподавателя:</strong> Вы можете выставлять оценки.
        </div>
    <?php else: ?>
        <div style="border: 2px solid blue; padding: 10px; margin: 10px 0;">
            <strong>Панель студента:</strong> Здесь отображаются ваши оценки.
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
        <select name="theme">
            <option value="light" <?= $theme === 'light' ? 'selected' : '' ?>>Светлая</option>
            <option value="dark" <?= $theme === 'dark' ? 'selected' : '' ?>>Тёмная</option>
        </select>
        <button type="submit">Сменить тему</button>
    </form>

    <form method="post" action="/logout.php">
        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
        <button type="submit">Выйти</button>
    </form>
</body>
</html>