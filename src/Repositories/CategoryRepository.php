<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;

/**
 * Репозиторий категорий.
 */
final class CategoryRepository
{
    public function __construct(
        private readonly string $file,
    ) {
    }

    /**
     * @return Category[]
     */
    public function all(): array
    {
        $rows = $this->loadRows();
        return array_map(
            static fn(array $row): Category => new Category(
                id: (int) $row['id'],
                name: (string) $row['name'],
            ),
            $rows
        );
    }

    public function findById(int $id): ?Category
    {
        foreach ($this->all() as $category) {
            if ($category->id === $id) {
                return $category;
            }
        }
        return null;
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
