<?php
declare(strict_types=1);
require "functions.php";
$products = require "catalog.php";

try {
    array_walk($products, fn(array $p) => validateProduct($p));

    $notExpired = filterNotExpired($products);
    $tablets = filterByForm($notExpired, "таблетки");
    $available = filterCatalog($tablets, null, 5000.0, true);
    $found = searchProducts($available, "витамин");
    $sorted = sortByPrice($found, true);
    $final = discounted($sorted, 0.10);

    echo "<h1>Каталог лекарственных товаров</h1>";
    echo "<h2>Таблетки, не просроченные, в наличии, до 5000 ₸ (со скидкой 10 %)</h2>";
    echo renderTable($final);

    echo "<p><b>Всего позиций в каталоге:</b> " . count($products) . "</p>";
    echo "<p><b>Просроченных:</b> " . count(array_filter($products, fn($p) => isExpired($p))) . "</p>";
    echo "<p><b>Стоимость запасов (весь каталог):</b> "
        . number_format(inventoryValue($products), 2, ".", " ") . " ₸</p>";

} catch (InvalidArgumentException $ex) {
    echo "<p style='color:red'>Ошибка данных: " . e($ex->getMessage()) . "</p>";
}