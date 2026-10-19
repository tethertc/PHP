<?php
declare(strict_types=1);
require 'functions.php';

$products = getProducts();
$cart = $_SESSION['cart'] ?? [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $id = (int)($_POST['id'] ?? 0);

        if (isset($_POST['update_qty'])) {
            $qty = (int)$_POST['qty'];
            if ($qty > 0) {
                $_SESSION['cart'][$id] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
            header("Location: cart.php");
            exit;
        }

        if (isset($_POST['remove'])) {
            unset($_SESSION['cart'][$id]);
            header("Location: cart.php");
            exit;
        }

        if (isset($_POST['checkout'])) {
            if (empty($cart)) {
                $message = "Корзина пуста!";
            } else {
                $order = [
                    'id' => uniqid('order_'),
                    'date' => date('Y-m-d H:i:s'),
                    'items' => [],
                    'total' => 0
                ];

                foreach ($cart as $p_id => $qty) {
                    $product = array_filter($products, fn($p) => $p['id'] === $p_id);
                    $product = reset($product);
                    if ($product) {
                        $sum = $product['price'] * $qty;
                        $order['items'][] = [
                            'product_id' => $p_id,
                            'name' => $product['name'],
                            'qty' => $qty,
                            'price' => $product['price'],
                            'sum' => $sum
                        ];
                        $order['total'] += $sum;
                    }
                }

                $ordersFile = 'orders.json';
                $orders = file_exists($ordersFile) ? json_decode(file_get_contents($ordersFile), true) : [];
                if (!is_array($orders)) $orders = [];
                
                $orders[] = $order;
                file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

                $_SESSION['cart'] = [];
                $cart = [];
                $_SESSION['message'] = "Заказ успешно оформлен! Номер: " . $order['id'];
                header("Location: cart.php");
                exit;
            }
        }
    } else {
        $message = "Ошибка безопасности (CSRF)!";
    }
}

$cart = $_SESSION['cart'] ?? [];
$message = $_SESSION['message'] ?? $message;
unset($_SESSION['message']);

$total = 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Корзина</title></head>
<body>
    <h1>Ваша корзина</h1>
    <p><a href="index.php">← Вернуться в каталог</a></p>

    <?php if ($message): ?>
        <p style="color:green;"><b><?= h($message) ?></b></p>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <p>Корзина пуста.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th>Сумма</th>
                <th>Действие</th>
            </tr>
            <?php foreach ($cart as $id => $qty): ?>
                <?php
                $product = array_filter($products, fn($p) => $p['id'] === $id);
                $product = reset($product);
                if (!$product) continue;

                $sum = $product['price'] * $qty;
                $total += $sum;
                ?>
                <tr>
                    <td><?= h($product['name']) ?></td>
                    <td><?= $product['price'] ?> тр.</td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="number" name="qty" value="<?= $qty ?>" min="0" style="width:50px;">
                            <button type="submit" name="update_qty">ОК</button>
                        </form>
                    </td>
                    <td><?= $sum ?> тр.</td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button type="submit" name="remove">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Итого:773 <?= $total ?> тр.</h2>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <button type="submit" name="checkout" style="padding: 10px 20px; font-size: 16px;">Оформить заказ</button>
        </form>
    <?php endif; ?>
</body>
</html>