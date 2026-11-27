<?php

declare(strict_types=1);

/**
 * Точка входа приложения.
 * Session запускается централизованно здесь.
 * Простой роутинг + автозагрузка классов.
 */

// Session — один раз в bootstrap, не в view
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Автозагрузка через spl_autoload_register (namespace App\...)
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CartService;

// Пути к dataset
$basePath = dirname(__DIR__);
$productsFile = $basePath . '/dataset/products.json';
$categoriesFile = $basePath . '/dataset/categories.json';

// Внедрение зависимостей
$productRepository = new ProductRepository($productsFile);
$categoryRepository = new CategoryRepository($categoriesFile);
$cartService = new CartService($productRepository);

$catalogController = new CatalogController($productRepository, $categoryRepository, $cartService);
$cartController = new CartController($cartService);

// Роутер
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim((string) $uri, '/') ?: '/';
$path = $uri;

if (str_starts_with($path, '/index.php')) {
    $path = substr($path, strlen('/index.php')) ?: '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// --- Catalog ---
if ($path === '/' || $path === '/catalog') {
    $catalogController->index();
    exit;
}

if (preg_match('#^/catalog/(\d+)$#', $path, $matches)) {
    $catalogController->show((int) $matches[1]);
    exit;
}

// --- Cart ---
if ($path === '/cart' && $method === 'GET') {
    $cartController->index();
    exit;
}

if ($path === '/cart/add' && $method === 'POST') {
    $cartController->add();
    exit;
}

if ($path === '/cart/update' && $method === 'POST') {
    $cartController->update();
    exit;
}

if ($path === '/cart/remove' && $method === 'POST') {
    $cartController->remove();
    exit;
}

if ($path === '/cart/clear' && $method === 'POST') {
    $cartController->clear();
    exit;
}

// 404
http_response_code(404);
$cartCount = $cartService->count();
$flash = $cartService->getFlash();
$message = 'Запрошенная страница не найдена.';
ob_start();
require dirname(__DIR__) . '/views/not-found.php';
$content = ob_get_clean();
require dirname(__DIR__) . '/views/layouts/main.php';
