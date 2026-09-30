<?php
declare(strict_types=1);
require "functions.php";
$products = require "catalog.php";

$pass = 0; $fail = 0;
function ok(bool $cond, string $label): void {
    global $pass, $fail;
    if ($cond) { echo "✅ $label\n"; $pass++; }
    else       { echo "❌ $label\n"; $fail++; }
}

$ok = true;
try { array_walk($products, fn($p) => validateProduct($p)); }
catch (Throwable $e) { $ok = false; }
ok($ok, "Тест 1. Валидация всего каталога");

$r1 = searchProducts($products, "ВИТАМИН");
$r2 = searchProducts($products, "витамин");
ok(count($r1) === count($r2) && count($r1) > 0, "Тест 2. Поиск не зависит от регистра");

ok(normalizeText("   Мазь   Вишневского  ") === "мазь вишневского", "Тест 3. Нормализация пробелов и регистра");

ok(count(searchProducts($products, "нет-такого-товара")) === 0, "Тест 4. Пустой результат поиска");

$caught = false;
try {
    validateProduct(["id"=>1,"name"=>"x","category"=>"y","form"=>"таблетки","expiry_date"=>"2027-01-01","price"=>-5,"stock"=>1,"created_at"=>"2026-09-01"]);
} catch (InvalidArgumentException $e) { $caught = true; }
ok($caught, "Тест 5. Отрицательная цена → InvalidArgumentException");

$caught = false;
try {
    validateProduct(["id"=>1,"name"=>"x","category"=>"y","expiry_date"=>"2027-01-01","price"=>10,"stock"=>1,"created_at"=>"2026-09-01"]);
} catch (InvalidArgumentException $e) { $caught = (str_contains($e->getMessage(), "form")); }
ok($caught, "Тест 6. Отсутствует ключ form → ошибка с именем поля");

$caught = false;
try {
    validateProduct(["id"=>1,"name"=>"x","category"=>"y","form"=>"таблетки","expiry_date"=>"2027-01-01","price"=>10,"stock"=>1,"created_at"=>"2026-13-99"]);
} catch (InvalidArgumentException $e) { $caught = true; }
ok($caught, "Тест 7. Некорректный created_at → исключение");

$caught = false;
try {
    validateProduct(["id"=>1,"name"=>"x","category"=>"y","form"=>"таблетки","expiry_date"=>"abc","price"=>10,"stock"=>1,"created_at"=>"2026-09-01"]);
} catch (InvalidArgumentException $e) { $caught = true; }
ok($caught, "Тест 8. Некорректный expiry_date → исключение");

ok(e("<script>alert(1)</script>") === "&lt;script&gt;alert(1)&lt;/script&gt;", "Тест 9. htmlspecialchars");

ok(isExpired(["expiry_date"=>"2020-01-01"], "2026-10-01") === true, "Тест 10. Просроченный товар определяется");

ok(isExpired(["expiry_date"=>"2030-01-01"], "2026-10-01") === false, "Тест 11. Непросроченный товар");

$tab = filterByForm($products, "таблетки");
$allTab = true;
foreach ($tab as $p) { if (normalizeText($p["form"]) !== "таблетки") $allTab = false; }
ok($allTab && count($tab) > 0, "Тест 12. Фильтр по форме работает");

echo "\n==========\n";
echo "Пройдено: $pass\nПровалено: $fail\n";