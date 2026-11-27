<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;

/**
 * Репозиторий товаров.
 * Читает JSON и возвращает готовые объекты Product.
 * Контроллер и View не должны знать формат хранения.
 */
final class ProductRepository
{
    public function __construct(
        private readonly string $file,
    ) {
    }

    /**
     * @return Product[]
     */
    public function all(): array
    {
        $rows = $this->loadRows();
        return array_map(
            static fn(array $row): Product => new Product(
                id: (int) $row['id'],
                name: (string) $row['name'],
                price: (float) $row['price'],
                categoryId: (int) $row['category_id'],
                stock: (int) $row['stock'],
            ),
            $rows
        );
    }

    public function findById(int $id): ?Product
    {
        foreach ($this->all() as $product) {
            if ($product->id === $id) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Фильтрация и сортировка на стороне репозитория / сервиса данных.
     *
     * @return Product[]
     */
    public function filter(?int $categoryId = null, string $sort = 'name'): array
    {
        $products = $this->all();

        if ($categoryId !== null) {
            $products = array_values(array_filter(
                $products,
                static fn(Product $p): bool => $p->categoryId === $categoryId
            ));
        }

        usort($products, static function (Product $a, Product $b) use ($sort): int {
            return match ($sort) {
                'price' => $a->price <=> $b->price,
                'price_desc' => $b->price <=> $a->price,
                'name_desc' => $b->name <=> $a->name,
                default => $a->name <=> $b->name, // name
            };
        });

        return $products;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadRows(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $json = file_get_contents($this->file);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }
}
