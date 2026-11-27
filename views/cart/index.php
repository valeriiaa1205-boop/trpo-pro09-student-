<?php
/** @var list<array{product: App\Models\Product, quantity: int, lineTotal: float}> $lines */
/** @var float $total */
/** @var string $formattedTotal */
/** @var bool $isEmpty */
?>
<div class="hero">
    <span class="pill">корзина</span>
    <h1>Ваша корзина</h1>
    <p class="muted">
        Состояние хранится в session (только id → количество).
        Данные о товарах и сумма считаются на сервере.
    </p>
</div>

<?php if ($isEmpty): ?>
    <div class="empty">
        <h2>Корзина пуста</h2>
        <p class="muted">Добавьте товары из каталога.</p>
        <a class="btn" href="/catalog">Перейти в каталог</a>
    </div>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>Товар</th>
            <th>Цена</th>
            <th>Кол-во</th>
            <th>Сумма</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lines as $line): ?>
            <?php
            $product = $line['product'];
            $qty = $line['quantity'];
            $lineTotal = $line['lineTotal'];
            ?>
            <tr>
                <td>
                    <a href="/catalog/<?= (int) $product->id ?>" style="color:inherit;font-weight:600;text-decoration:none;">
                        <?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($product->formattedPrice(), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <form method="post" action="/cart/update" class="inline-form">
                        <input type="hidden" name="product_id" value="<?= (int) $product->id ?>">
                        <input type="number" name="quantity" value="<?= (int) $qty ?>" min="1" max="99" style="width:70px;">
                        <button type="submit" class="btn btn-sm btn-outline">OK</button>
                    </form>
                </td>
                <td><strong><?= number_format($lineTotal, 0, ',', ' ') ?> ₽</strong></td>
                <td>
                    <form method="post" action="/cart/remove" class="inline-form">
                        <input type="hidden" name="product_id" value="<?= (int) $product->id ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-summary">
        <div>
            <span class="muted">Итого:</span>
            <div class="total"><?= htmlspecialchars($formattedTotal, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="post" action="/cart/clear">
                <button type="submit" class="btn btn-outline">Очистить корзину</button>
            </form>
            <a class="btn" href="/catalog">Продолжить покупки</a>
        </div>
    </div>
<?php endif; ?>
