<?php
declare(strict_types=1);

function normalizeText(string $value): string {
    $value = trim($value);
    $value = preg_replace("/\s+/u", " ", $value) ?? $value;
    return mb_strtolower($value, "UTF-8");
}

function validateProduct(array $product): void {
    $required = ["id", "name", "category", "form", "expiry_date", "price", "stock", "created_at"];
    foreach ($required as $key) {
        if (!array_key_exists($key, $product)) {
            throw new InvalidArgumentException("Отсутствует поле: $key");
        }
    }
    if (!is_int($product["id"]) || $product["id"] <= 0) {
        throw new InvalidArgumentException("Некорректный id");
    }
    if (trim((string)$product["name"]) === "") {
        throw new InvalidArgumentException("Пустое название");
    }
    if (trim((string)$product["category"]) === "") {
        throw new InvalidArgumentException("Пустая категория");
    }
    if (trim((string)$product["form"]) === "") {
        throw new InvalidArgumentException("Пустая форма");
    }
    if (!is_numeric($product["price"]) || (float)$product["price"] < 0) {
        throw new InvalidArgumentException("Некорректная цена");
    }
    if (!is_int($product["stock"]) || $product["stock"] < 0) {
        throw new InvalidArgumentException("Некорректный остаток");
    }
    $created = DateTimeImmutable::createFromFormat("Y-m-d", (string)$product["created_at"]);
    if ($created === false || $created->format("Y-m-d") !== $product["created_at"]) {
        throw new InvalidArgumentException("Некорректная дата создания (created_at)");
    }
    $expiry = DateTimeImmutable::createFromFormat("Y-m-d", (string)$product["expiry_date"]);
    if ($expiry === false || $expiry->format("Y-m-d") !== $product["expiry_date"]) {
        throw new InvalidArgumentException("Некорректная дата срока годности (expiry_date)");
    }
}

function isExpired(array $product, ?string $today = null): bool {
    $today = $today ?? date("Y-m-d");
    return (string)$product["expiry_date"] < $today;
}

function searchProducts(array $items, string $query): array {
    $query = normalizeText($query);
    if ($query === "") {
        return $items;
    }
    return array_values(array_filter($items, fn(array $p): bool =>
        mb_stripos(normalizeText((string)$p["name"]), $query, 0, "UTF-8") !== false
    ));
}

function filterCatalog(
    array $items,
    ?string $category = null,
    ?float $maxPrice = null,
    bool $onlyAvailable = false
): array {
    $category = $category === null ? null : normalizeText($category);
    return array_values(array_filter($items, function (array $p) use ($category, $maxPrice, $onlyAvailable): bool {
        if ($category !== null && normalizeText((string)$p["category"]) !== $category) {
            return false;
        }
        if ($maxPrice !== null && (float)$p["price"] > $maxPrice) {
            return false;
        }
        return !$onlyAvailable || $p["stock"] > 0;
    }));
}

function filterByForm(array $items, string $form): array {
    $form = normalizeText($form);
    if ($form === "") {
        return $items;
    }
    return array_values(array_filter($items, fn(array $p): bool =>
        normalizeText((string)$p["form"]) === $form
    ));
}

function filterNotExpired(array $items, ?string $today = null): array {
    return array_values(array_filter($items, fn(array $p): bool => !isExpired($p, $today)));
}

function sortByPrice(array $items, bool $ascending = true): array {
    usort($items, fn(array $a, array $b): int =>
        $ascending ? $a["price"] <=> $b["price"] : $b["price"] <=> $a["price"]
    );
    return $items;
}

function inventoryValue(array $items): float {
    return round(array_reduce(
        $items,
        fn(float $sum, array $p): float => $sum + (float)$p["price"] * (int)$p["stock"],
        0.0
    ), 2);
}

function discounted(array $items, float $rate): array {
    if ($rate < 0 || $rate > 1) {
        throw new InvalidArgumentException("Неверная скидка (0..1)");
    }
    return array_map(function (array $p) use ($rate): array {
        $p["final_price"] = round((float)$p["price"] * (1 - $rate), 2);
        return $p;
    }, $items);
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function renderTable(array $items): string {
    $html = "<table border='1' cellpadding='6' cellspacing='0'>";
    $html .= "<tr><th>ID</th><th>Название</th><th>Категория</th>"
           . "<th>Форма</th><th>Срок годности</th><th>Цена, ₸</th>"
           . "<th>Цена со скидкой, ₸</th><th>Остаток</th></tr>";
    foreach ($items as $p) {
        $final = isset($p["final_price"]) ? number_format((float)$p["final_price"], 2, ".", " ") : "—";
        $html .= "<tr>"
               . "<td>" . (int)$p["id"] . "</td>"
               . "<td>" . e((string)$p["name"]) . "</td>"
               . "<td>" . e((string)$p["category"]) . "</td>"
               . "<td>" . e((string)$p["form"]) . "</td>"
               . "<td>" . e((string)$p["expiry_date"]) . "</td>"
               . "<td>" . number_format((float)$p["price"], 2, ".", " ") . "</td>"
               . "<td>" . $final . "</td>"
               . "<td>" . (int)$p["stock"] . "</td>"
               . "</tr>";
    }
    return $html . "</table>";
}