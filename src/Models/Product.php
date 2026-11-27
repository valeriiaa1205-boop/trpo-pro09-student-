<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Модель товара каталога.
 * Отвечает только за данные сущности, без логики чтения файлов и HTML.
 */
final class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $price,
        public readonly int $categoryId,
        public readonly int $stock,
    ) {
    }

    /**
     * Форматирование цены для отображения.
     */
    public function formattedPrice(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }

    /**
     * Есть ли товар в наличии.
     */
    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Человекочитаемый статус наличия.
     */
    public function stockLabel(): string
    {
        if ($this->stock <= 0) {
            return 'Нет в наличии';
        }
        if ($this->stock <= 3) {
            return 'Мало (' . $this->stock . ')';
        }
        return 'В наличии (' . $this->stock . ')';
    }
}
