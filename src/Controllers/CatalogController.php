<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CartService;

/**
 * Контроллер каталога.
 * Получает репозитории и CartService через конструктор.
 */
final class CatalogController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CartService $cart,
    ) {
    }

    public function index(): void
    {
        $categoryParam = $_GET['category'] ?? null;
        $sort = $_GET['sort'] ?? 'name';

        $allowedSorts = ['name', 'name_desc', 'price', 'price_desc'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $categoryId = null;
        if ($categoryParam !== null && $categoryParam !== '') {
            $categoryId = (int) $categoryParam;
        }

        $products = $this->productRepository->filter($categoryId, $sort);
        $categories = $this->categoryRepository->all();

        $currentCategory = null;
        if ($categoryId !== null) {
            $currentCategory = $this->categoryRepository->findById($categoryId);
        }

        $this->render('catalog/index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategoryId' => $categoryId,
            'currentCategory' => $currentCategory,
            'sort' => $sort,
            'isEmpty' => count($products) === 0,
            'isUnknownCategory' => $categoryId !== null && $currentCategory === null,
            'flash' => $this->cart->getFlash(),
            'cartCount' => $this->cart->count(),
        ]);
    }

    public function show(int $id): void
    {
        $product = $this->productRepository->findById($id);

        if ($product === null) {
            http_response_code(404);
            $this->render('not-found', [
                'message' => 'Товар с идентификатором ' . $id . ' не найден.',
                'cartCount' => $this->cart->count(),
                'flash' => $this->cart->getFlash(),
            ]);
            return;
        }

        $category = $this->categoryRepository->findById($product->categoryId);

        $this->render('catalog/show', [
            'product' => $product,
            'category' => $category,
            'flash' => $this->cart->getFlash(),
            'cartCount' => $this->cart->count(),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__, 2) . '/views/' . $view . '.php';
        $layoutFile = dirname(__DIR__, 2) . '/views/layouts/main.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view);
            return;
        }

        if (!isset($cartCount)) {
            $cartCount = $this->cart->count();
        }
        if (!isset($flash)) {
            $flash = null;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }
}
