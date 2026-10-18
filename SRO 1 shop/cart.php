<?php
require 'functions.php';
$products = getProducts();
$cart = $_SESSION['cart'] ?? [];
$message = '';

// Обновление или удаление из корзины
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (checkCsrfToken($_POST['csrf_token'] ?? null)) {
        $id = (int)$_POST['id'];
        
        if (isset($_POST['update_qty'])) {
            $qty = (int)$_POST['qty'];
            if ($qty > 0) {
                $_SESSION['cart'][$id] = $qty;
            } else {
                unset($_SESSION['cart'][$id]); // Если 0, удаляем
            }
            header("Location: cart.php"); // Перезагрузка страницы
            exit;
        }
        
        if (isset($_POST['remove'])) {
            unset($_SESSION['cart'][$id]);
            header("Location: cart.php");
            exit;
        }
        
        if (isset($_POST['checkout'])) {
            $_SESSION['cart'] = []; // Очищаем корзину
            $message = "Заказ успешно оформлен! (Учебная симуляция)";
        }
    }
}

// Считаем итоговую сумму на СЕРВЕРЕ
$total = 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Корзина</title></head>
<body>
    <h1>Ваша корзина</h1>
    <p><a href="index.php">← Вернуться в каталог</a></p>
    
    <?php if ($message): ?><p style="color:green;"><b><?= h($message) ?></b></p><?php endif; ?>

    <?php if (empty($cart)): ?>
        <p>Корзина пуста.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th><th>Действие</th></tr>
            
            <?php foreach ($cart as $id => $qty): 
                // Ищем товар в базе по ID
                $product = array_filter($products, fn($p) => $p['id'] === $id);
                $product = reset($product); 
                if (!$product) continue;
                
                $sum = $product['price'] * $qty;
                $total += $sum; // Суммируем
            ?>
            <tr>
                <td><?= h($product['name']) ?></td>
                <td><?= $product['price'] ?> тг.</td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="number" name="qty" value="<?= $qty ?>" min="0" style="width:50px;">
                        <button type="submit" name="update_qty">ОК</button>
                    </form>
                </td>
                <td><?= $sum ?> тг.</td>
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
        
        <h2>Итого: <?= $total ?> тг.</h2>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
            <button type="submit" name="checkout" style="padding: 10px 20px; font-size: 16px;">Оформить заказ</button>
        </form>
    <?php endif; ?>
</body>
</html>