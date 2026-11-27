<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

/**
 * Сервис корзины.
 * В session хранится только productId => quantity.
 * Данные о товарах подтягиваются через ProductRepository.
 */
final class CartService
{
    private const SESSION_KEY = 'cart';
    private const FLASH_KEY = 'flash';

    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    /**
     * Добавить товар (увеличить количество на 1).
     */
    public function add(int $productId): bool
    {
        $product = $this->productRepository->findById($productId);
        if ($product === null) {
            return false;
        }

        $qty = (int) ($_SESSION[self::SESSION_KEY][$productId] ?? 0);
        $_SESSION[self::SESSION_KEY][$productId] = $qty + 1;
        $this->setFlash('Товар «' . $product->name . '» добавлен в корзину.');
        return true;
    }

    /**
     * Установить количество. 0 или меньше — удаляет позицию.
     */
    public function setQuantity(int $productId, int $quantity): bool
    {
        $product = $this->productRepository->findById($productId);
        if ($product === null) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        $_SESSION[self::SESSION_KEY][$productId] = $quantity;
        $this->setFlash('Количество обновлено.');
        return true;
    }

    /**
     * Удалить позицию.
     */
    public function remove(int $productId): bool
    {
        if (!isset($_SESSION[self::SESSION_KEY][$productId])) {
            return false;
        }

        unset($_SESSION[self::SESSION_KEY][$productId]);
        $this->setFlash('Товар удалён из корзины.');
        return true;
    }

    /**
     * Очистить корзину.
     */
    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
        $this->setFlash('Корзина очищена.');
    }

    /**
     * Сырые данные: productId => quantity
     *
     * @return array<int, int>
     */
    public function raw(): array
    {
        $result = [];
        foreach ($_SESSION[self::SESSION_KEY] as $id => $qty) {
            $result[(int) $id] = (int) $qty;
        }
        return $result;
    }

    /**
     * Готовые строки корзины для view.
     *
     * @return list<array{product: Product, quantity: int, lineTotal: float}>
     */
    public function lines(): array
    {
        $lines = [];
        foreach ($this->raw() as $productId => $quantity) {
            $product = $this->productRepository->findById($productId);
            if ($product === null) {
                continue;
            }
            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'lineTotal' => $product->price * $quantity,
            ];
        }
        return $lines;
    }

    /**
     * Общая сумма.
     */
    public function total(): float
    {
        $sum = 0.0;
        foreach ($this->lines() as $line) {
            $sum += $line['lineTotal'];
        }
        return $sum;
    }

    /**
     * Количество позиций (сумма quantity).
     */
    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function formattedTotal(): string
    {
        return number_format($this->total(), 0, ',', ' ') . ' ₽';
    }

    // --- Flash ---

    public function setFlash(string $message): void
    {
        $_SESSION[self::FLASH_KEY] = $message;
    }

    public function getFlash(): ?string
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            return null;
        }
        $msg = (string) $_SESSION[self::FLASH_KEY];
        unset($_SESSION[self::FLASH_KEY]);
        return $msg;
    }
}
