```php
<?php
declare(strict_types=1);

function validatePrice(float $price): void {
    if ($price <= 0) {
        throw new InvalidArgumentException("Price must be greater than zero.");
    }
}

function validateQuantity(int $qty): void {
    if ($qty <= 0) {
        throw new InvalidArgumentException("Quantity must be greater than zero.");
    }
}

function calculateItemTotal(float $price, int $qty, string|int $discountPercent = 0): float {
    validatePrice($price);
    validateQuantity($qty);
    
    if ((float)$discountPercent < 0 || (float)$discountPercent > 100) {
        throw new InvalidArgumentException("Discount must be between 0 and 100.");
    }
    
    $total = $price * $qty;
    $discount = $total * ((float)$discountPercent / 100);
    return round($total - $discount, 2);
}

function formatReceipt(string $itemName, float $total, ?string $note = null): string {
    if (trim($itemName) === '') {
        throw new InvalidArgumentException("Item name cannot be empty.");
    }
    
    $html = "<div><b>" . htmlspecialchars($itemName) . "</b>: " . number_format($total, 2) . " KZT</div>";
    if ($note !== null) {
        $html .= "<small style='color:gray;'> (Note: " . htmlspecialchars($note) . ")</small>";
    }
    return $html;
}

function processOrder(array $items, float $taxRate): array {
    $subtotal = 0;
    $itemNames = [];

    foreach ($items as $item) {
        $itemTotal = calculateItemTotal($item['price'], $item['qty'], $item['discount'] ?? 0);
        $subtotal += $itemTotal;
        $itemNames[] = $item['name'];
    }

    $formattedNames = array_map(fn(string $name): string => strtoupper($name), $itemNames);

    $applyTax = function(float $amount) use ($taxRate): float {
        return round($amount * (1 + $taxRate), 2);
    };

    $finalTotal = $applyTax($subtotal);

    return [
        'items' => $formattedNames,
        'subtotal' => $subtotal,
        'tax_rate' => $taxRate * 100,
        'final_total' => $finalTotal
    ];
}
```