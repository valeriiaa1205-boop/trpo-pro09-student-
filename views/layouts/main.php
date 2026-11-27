<?php
/** @var string $content */
/** @var int|null $cartCount */
/** @var string|null $flash */
$cartCount = $cartCount ?? 0;
$flash = $flash ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Каталог — Практическая работа 9</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4f6fb; color: #1f2937; font-family: Arial, Helvetica, sans-serif; line-height: 1.5; }
        header { background: #111827; color: white; padding: 18px 32px; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
        .brand { font-weight: 800; font-size: 20px; text-decoration: none; color: white; }
        .header-nav { display: flex; align-items: center; gap: 16px; }
        .header-nav a { color: #e5e7eb; text-decoration: none; font-size: 14px; }
        .header-nav a:hover { color: white; }
        .cart-badge { display: inline-flex; align-items: center; gap: 8px; background: #315efb; color: white; padding: 8px 14px; border-radius: 999px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .cart-badge:hover { background: #2548d4; }
        .cart-count { background: white; color: #315efb; border-radius: 999px; min-width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; padding: 0 6px; }
        .wrap { max-width: 1100px; margin: 28px auto; padding: 0 20px 40px; }
        .flash { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 12px; margin-bottom: 18px; }
        .hero { background: white; border-radius: 16px; padding: 28px 32px; box-shadow: 0 10px 30px rgba(0,0,0,.05); margin-bottom: 22px; }
        .hero h1 { margin: 8px 0 10px; font-size: 26px; }
        .muted { color: #6b7280; }
        .pill { display: inline-block; background: #e9efff; color: #315efb; border-radius: 999px; padding: 4px 10px; font-size: 13px; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 20px; }
        .toolbar form { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
        select, .btn, input[type="number"] { padding: 9px 14px; border-radius: 10px; border: 1px solid #d1d5db; font-size: 14px; background: white; }
        .btn { background: #315efb; color: white; border: none; text-decoration: none; cursor: pointer; display: inline-block; }
        .btn:hover { background: #2548d4; }
        .btn-outline { background: white; color: #315efb; border: 1px solid #315efb; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-sm { padding: 6px 10px; font-size: 13px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .card { background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px; display: flex; flex-direction: column; gap: 8px; transition: box-shadow .15s; }
        .card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        .card .name { font-weight: 700; font-size: 16px; }
        .card .price { font-size: 18px; font-weight: 800; color: #111827; }
        .card .meta { font-size: 13px; color: #6b7280; }
        .stock-ok { color: #059669; }
        .stock-low { color: #d97706; }
        .stock-out { color: #dc2626; }
        .empty { background: white; border-radius: 14px; padding: 40px 24px; text-align: center; border: 1px dashed #d1d5db; }
        .empty h2 { margin-top: 0; }
        .table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        .table th, .table td { border-bottom: 1px solid #e5e7eb; padding: 12px 14px; text-align: left; vertical-align: middle; }
        .table th { background: #f9fafb; font-size: 13px; color: #6b7280; }
        .product-detail { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,.05); }
        .product-detail h1 { margin-top: 0; }
        .back-link { display: inline-block; margin-bottom: 16px; color: #315efb; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .cart-summary { background: white; border-radius: 14px; padding: 20px 24px; margin-top: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.05); }
        .cart-summary .total { font-size: 22px; font-weight: 800; }
        .inline-form { display: inline-flex; gap: 6px; align-items: center; }
        footer { text-align: center; color: #9ca3af; font-size: 13px; padding: 24px; }
        @media (max-width: 640px) { header { padding: 14px 16px; flex-wrap: wrap; } .hero { padding: 20px; } .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header>
    <a class="brand" href="/catalog">Практическая работа 9</a>
    <div class="header-nav">
        <a href="/catalog">Каталог</a>
        <a class="cart-badge" href="/cart">
            Корзина
            <span class="cart-count"><?= (int) $cartCount ?></span>
        </a>
    </div>
</header>
<div class="wrap">
    <?php if ($flash): ?>
        <div class="flash"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?= $content ?>
</div>
<footer>
    Session + CartService · POST → Redirect → GET · Итог считается на сервере
</footer>
</body>
</html>
