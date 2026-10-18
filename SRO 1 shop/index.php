<?php
require 'functions.php';
$products = getProducts();
$search = $_GET['search'] ?? '';

// Если есть поиск — фильтруем массив
if ($search !== '') {
    $products = array_filter($products, function($p) use ($search) {
        return mb_stripos($p['name'], $search) !== false;
    });
}

// Добавление в корзину (POST запрос)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $id = (int)$_POST['id'];
        // В корзине храним только ID и количество
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        $message = "Товар добавлен в корзину!";
    } else {
        $message = "Ошибка безопасности (CSRF)!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Магазин учебных товаров</title></head>
<body>
    <h1>Каталог товаров</h1>
    <p><a href="cart.php">Перейти в корзину (Товаров: <?= array_sum($_SESSION['cart'] ?? []) ?>)</a></p>
    
    <?php if ($message): ?><p style="color:green;"><b><?= h($message) ?></b></p><?php endif; ?>

    <form method="GET">
        <input type="text" name="search" placeholder="Поиск по названию..." value="<?= h($search) ?>">
        <button type="submit">Найти</button>
        <a href="index.php">Сбросить</a>
    </form>
    <br>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Название</th><th>Категория</th><th>Цена</th><th>Действие</th>
        </tr>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= h($p['name']) ?></td>
            <td><?= h($p['category']) ?></td>
            <td><?= $p['price'] ?> тг.</td>
            <td>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" name="add_to_cart">Добавить</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>