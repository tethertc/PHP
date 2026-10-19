<?php
declare(strict_types=1);
require 'functions.php';

$products = getProducts();
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

$products = array_filter($products, function($p) use ($search, $category) {
    $matchSearch = $search === '' || mb_stripos($p['name'], $search) !== false;
    $matchCategory = $category === '' || $p['category'] === $category;
    return $matchSearch && $matchCategory;
});

if ($sort === 'price_asc') {
    usort($products, fn($a, $b) => $a['price'] <=> $b['price']);
} elseif ($sort === 'price_desc') {
    usort($products, fn($a, $b) => $b['price'] <=> $a['price']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $id = (int)$_POST['id'];
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        $_SESSION['message'] = "Товар добавлен в корзину!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = "Ошибка безопасности (CSRF)!";
    }
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Магазин учебных товаров</title></head>
<body>
    <h1>Каталог товаров</h1>
    
    <p>
        <a href="cart.php">Перейти в корзину (Товаров: <?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
    </p>

    <?php if ($message): ?>
        <p style="color:green;"><b><?= h($message) ?></b></p>
    <?php endif; ?>

    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Поиск по названию..." value="<?= h($search) ?>">
        
        <select name="category">
            <option value="">Все категории</option>
            <option value="Бумага" <?= $category === 'Бумага' ? 'selected' : '' ?>>Бумага</option>
            <option value="Канцелярия" <?= $category === 'Канцелярия' ? 'selected' : '' ?>>Канцелярия</option>
            <option value="Аксессуары" <?= $category === 'Аксессуары' ? 'selected' : '' ?>>Аксессуары</option>
        </select>

        <select name="sort">
            <option value="">Без сортировки</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена (по возрастанию)</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена (по убыванию)</option>
        </select>

        <button type="submit">Найти</button>
        <a href="index.php">Сбросить</a>
    </form>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Наименование</th>
            <th>Категория</th>
            <th>Цена</th>
            <th>Действие</th>
        </tr>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><a href="product.php?id=<?= $p['id'] ?>"><?= h($p['name']) ?></a></td>
                <td><?= h($p['category']) ?></td>
                <td><?= $p['price'] ?> тр.</td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" name="add_to_cart">Добавить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (empty($products)): ?>
            <tr>
                <td colspan="4" style="text-align:center;">Товары не найдены</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>>
           <option value="Маркер" <?= $category === 'Черный маркер
           ' ? 'selected' : '' ?>>Аксессуары</option>