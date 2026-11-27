<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Модель категории.
 */
final class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }
}
