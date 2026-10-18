```php
<?php
declare(strict_types=1);
require_once 'functions.php';

echo "<h2>Test Results</h2>";

function runTest(string $name, callable $callback): void {
    static $testNum = 1;
    echo "<b>Test $testNum: $name</b> — ";
    try {
        $callback();
        echo "<span style='color: green;'>PASS</span><br>";
    } catch (Throwable $e) {
        echo "<span style='color: red;'>FAIL: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
    $testNum++;
}

runTest("Normal calculation", function() {
    $res = calculateItemTotal(100.0, 2); 
    if ($res !== 200.0) throw new Exception("Expected 200");
});

runTest("Maximum discount", function() {
    $res = calculateItemTotal(100.0, 1, 100);
    if ($res !== 0.0) throw new Exception("Expected 0");
});

runTest("Minimum valid price", function() {
    validatePrice(0.01); 
});

runTest("Negative price", function() {
    try {
        validatePrice(-10);
        throw new Exception("No exception thrown");
    } catch (InvalidArgumentException $e) {}
});

runTest("Zero quantity", function() {
    try {
        validateQuantity(0);
        throw new Exception("No exception thrown");
    } catch (InvalidArgumentException $e) {}
});

runTest("Empty item name", function() {
    try {
        formatReceipt("   ", 100.0);
        throw new Exception("No exception thrown");
    } catch (InvalidArgumentException $e) {}
});

runTest("Nullable parameter", function() {
    $res = formatReceipt("Item", 500, null);
    if (strpos($res, "Note:") !== false) throw new Exception("Note should not be present");
});

runTest("Strict types", function() {
    try {
        validatePrice("100.5"); 
        throw new Exception("TypeError not thrown");
    } catch (TypeError $e) {}
});
```