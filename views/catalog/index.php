<?php
/** @var App\Models\Product[] $products */
/** @var App\Models\Category[] $categories */
/** @var int|null $currentCategoryId */
/** @var App\Models\Category|null $currentCategory */
/** @var string $sort */
/** @var bool $isEmpty */
/** @var bool $isUnknownCategory */
?>
<div class="hero">
    <span class="pill">каталог товаров</span>
    <h1>PHP MVC интернет-магазин</h1>
    <p class="muted">
        Данные через Repository и модели. Корзина — session + CartService.
        Добавление только через POST с последующим redirect.
    </p>
</div>

<div class="toolbar">
    <form method="get" action="/catalog">
        <label>
            Категория:
            <select name="category" onchange="this.form.submit()">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat->id ?>"
                        <?= $currentCategoryId === $cat->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Сортировка:
            <select name="sort" onchange="this.form.submit()">
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Название (А–Я)</option>
                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Название (Я–А)</option>
                <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Цена (возр.)</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена (убыв.)</option>
            </select>
        </label>

        <?php if ($currentCategoryId !== null): ?>
            <input type="hidden" name="category" value="<?= (int) $currentCategoryId ?>">
        <?php endif; ?>
    </form>

    <?php if ($currentCategory !== null): ?>
        <span class="pill"><?= htmlspecialchars($currentCategory->name, ENT_QUOTES, 'UTF-8') ?></span>
    <?php endif; ?>

    <span class="muted">Найдено: <?= count($products) ?></span>
</div>

<?php if ($isUnknownCategory): ?>
    <div class="empty">
        <h2>Категория не найдена</h2>
        <p class="muted">Запрошена несуществующая категория (id = <?= (int) $currentCategoryId ?>).</p>
        <a class="btn" href="/catalog">Показать весь каталог</a>
    </div>
<?php elseif ($isEmpty): ?>
    <div class="empty">
        <h2>Товаров не найдено</h2>
        <p class="muted">По выбранным параметрам список пуст.</p>
        <a class="btn" href="/catalog">Сбросить фильтры</a>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($products as $product): ?>
            <article class="card">
                <div class="name">
                    <a href="/catalog/<?= (int) $product->id ?>" style="color:inherit;text-decoration:none;">
                        <?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </div>
                <div class="price"><?= htmlspecialchars($product->formattedPrice(), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="meta">Категория #<?= (int) $product->categoryId ?></div>
                <div class="<?= $product->inStock() ? ($product->stock <= 3 ? 'stock-low' : 'stock-ok') : 'stock-out' ?>">
                    <?= htmlspecialchars($product->stockLabel(), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div style="margin-top:auto;display:flex;gap:8px;flex-wrap:wrap;">
                    <a class="btn btn-outline btn-sm" href="/catalog/<?= (int) $product->id ?>">Подробнее</a>
                    <?php if ($product->inStock()): ?>
                        <form method="post" action="/cart/add" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= (int) $product->id ?>">
                            <input type="hidden" name="redirect" value="/catalog">
                            <button type="submit" class="btn btn-sm">В корзину</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
