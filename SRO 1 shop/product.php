<?php
declare(strict_types=1);
require 'functions.php';

$id = (int)($_GET['id'] ?? 0);
$products = getProducts();

$product = null;
foreach ($products as $p) {
    if ($p['id'] === $id) {
        $product = $p;
        break;
    }
}

if (!$product) {
    http_response_code(404);
    die("Товар не найден. <a href='index.php'>Вернуться в каталог</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        $_SESSION['message'] = "Товар добавлен в корзину!";
        header("Location: index.php");
        exit;
    }
}
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title><?= h($product['name']) ?></title></head>
<body>
    <p><a href="index.php">← Вернуться в каталог</a></p>
    
    <?php if ($message): ?>
        <p style="color:green;"><b><?= h($message) ?></b></p>
    <?php endif; ?>

    <h1><?= h($product['name']) ?></h1>
    <p><strong>Категория:</strong> <?= h($product['category']) ?></p>
    <p><strong>Цена:</strong> <?= $product['price'] ?> тр.</p>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
        <button type="submit" name="add_to_cart">Добавить в корзину</button>
    </form>
</body>
</html>