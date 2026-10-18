<?php
declare(strict_types=1);
session_start();

// 1. Получить все товары
function getProducts(): array {
    $json = file_get_contents('products.json');
    return json_decode($json, true) ?? [];
}

// 2. Защита от XSS (экранирование)
function h(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// 3. Генерация CSRF-токена
function getCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// 4. Проверка CSRF-токена
function checkCsrfToken(?string $token): bool {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}