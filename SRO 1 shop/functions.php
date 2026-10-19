<?php
declare(strict_types=1);
session_start();

function getProducts(): array {
    $json = file_get_contents('products.json');
    return json_decode($json, true) ?? [];
}

function h(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function getCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function checkCsrfToken(?string $token): bool {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}