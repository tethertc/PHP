```php
<?php
declare(strict_types=1);
require_once 'functions.php';

$cart = [
    ['name' => 'Laptop', 'price' => 450000, 'qty' => 1, 'discount' => 5],
    ['name' => 'Mouse', 'price' => 15000, 'qty' => 2, 'discount' => 0],
    ['name' => 'Mousepad', 'price' => 5000, 'qty' => 1, 'discount' => '10']
];

$tax = 0.12;

echo "<h2>Order Receipt</h2>";
echo "<div style='font-family: Arial; padding: 20px; border: 1px solid #ccc; width: 350px;'>";

foreach ($cart as $item) {
    $discount = is_numeric($item['discount']) ? (float)$item['discount'] : 0;
    $total = calculateItemTotal($item['price'], $item['qty'], $discount);
    $note = $discount > 0 ? "Discount {$discount}%" : null;
    echo formatReceipt($item['name'], $total, $note);
}

$order = processOrder($cart, $tax);

echo "<hr>";
echo "<div>Subtotal: " . number_format($order['subtotal'], 2) . " KZT</div>";
echo "<div>Tax: " . number_format($order['tax_rate'] * 100, 2) . "%</div>";
echo "<div><b>TOTAL: " . number_format($order['final_total'], 2) . " KZT</b></div>";
echo "<div style='color: green; margin-top: 10px;'>Processed Items: " . implode(', ', $order['items']) . "</div>";
echo "</div>";
```