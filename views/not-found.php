<?php
/** @var string|null $message */
$message = $message ?? 'Запрошенная страница не найдена.';
?>
<div class="hero">
    <span class="pill">404</span>
    <h1>Страница не найдена</h1>
    <p class="muted"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <p style="margin-top:20px;">
        <a class="btn" href="/catalog">Перейти в каталог</a>
    </p>
</div>
