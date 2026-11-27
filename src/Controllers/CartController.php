<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CartService;

/**
 * Контроллер корзины.
 * Изменяет состояние через CartService, затем redirect (POST → Redirect → GET).
 * View только отображает готовую корзину.
 */
final class CartController
{
    public function __construct(
        private readonly CartService $cart,
    ) {
    }

    /**
     * GET /cart — показать корзину.
     */
    public function index(): void
    {
        $this->render('cart/index', [
            'lines' => $this->cart->lines(),
            'total' => $this->cart->total(),
            'formattedTotal' => $this->cart->formattedTotal(),
            'isEmpty' => $this->cart->isEmpty(),
            'flash' => $this->cart->getFlash(),
            'cartCount' => $this->cart->count(),
        ]);
    }

    /**
     * POST /cart/add — добавить товар.
     */
    public function add(): void
    {
        $this->ensurePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        if ($productId <= 0 || !$this->cart->add($productId)) {
            $this->cart->setFlash('Не удалось добавить товар. Проверьте идентификатор.');
        }

        $redirect = $_POST['redirect'] ?? '/cart';
        $this->redirect($redirect);
    }

    /**
     * POST /cart/update — изменить количество.
     */
    public function update(): void
    {
        $this->ensurePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if ($productId <= 0) {
            $this->cart->setFlash('Некорректный идентификатор товара.');
            $this->redirect('/cart');
        }

        // quantity должен быть положительным целым; 0 и меньше — удаление
        $this->cart->setQuantity($productId, max(0, $quantity));
        $this->redirect('/cart');
    }

    /**
     * POST /cart/remove — удалить позицию.
     */
    public function remove(): void
    {
        $this->ensurePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        if ($productId > 0) {
            $this->cart->remove($productId);
        }

        $this->redirect('/cart');
    }

    /**
     * POST /cart/clear — очистить корзину.
     */
    public function clear(): void
    {
        $this->ensurePost();
        $this->cart->clear();
        $this->redirect('/cart');
    }

    private function ensurePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }
    }

    private function redirect(string $url): void
    {
        // Разрешаем только относительные пути внутри приложения
        if (!str_starts_with($url, '/')) {
            $url = '/cart';
        }
        header('Location: ' . $url);
        exit;
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
            echo 'View not found';
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // cartCount для layout, если не передан
        if (!isset($cartCount)) {
            $cartCount = $this->cart->count();
        }
        if (!isset($flash)) {
            $flash = $this->cart->getFlash();
        }

        require $layoutFile;
    }
}
