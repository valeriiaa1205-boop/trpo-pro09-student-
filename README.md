# Практическая работа №9 — PHP MVC интернет-магазин. Корзина

**Дисциплина:** Технологии разработки программного обеспечения  
**Тема:** Корзина, session и контроллер действий

## Результат

Рабочая корзина с:

- добавлением товара (POST `/cart/add`);
- изменением количества (POST `/cart/update`);
- удалением позиции (POST `/cart/remove`);
- очисткой корзины;
- расчётом итоговой стоимости **на сервере**;
- счётчиком в header;
- flash-сообщениями;
- пустым и заполненным состоянием.

Паттерн **POST → Redirect → GET**: обновление страницы не повторяет действие.

## Что хранится в session

```php
$_SESSION['cart'] = [
  productId => quantity,  // только id и количество
];
$_SESSION['flash'] = 'сообщение'; // один раз
```

Полные данные товара (название, цена) **не** дублируются в session — они берутся из `ProductRepository`.

## Где считается сумма

`CartService::total()` и `CartService::lines()` — в сервисном слое, не в HTML.

## Схема запроса

```
POST /cart/add  {product_id}
    → CartController::add()
    → CartService::add()
    → redirect /cart или /catalog
GET  /cart
    → CartController::index()
    → CartService::lines() + total()
    → views/cart/index.php
```

## Запуск

```bash
cd trpo-pr09
php -S localhost:8080 -t public
```

- http://localhost:8080/catalog
- http://localhost:8080/cart

## Сценарии cart_scenarios.json

| steps | expected |
|-------|----------|
| add:1, add:1, add:2 | {1:2, 2:1} |
| add:3, remove:3 | {} |
| add:4, set:4:3 | {4:3} |

## Структура

```
public/index.php          # session_start + router
src/
  Controllers/CatalogController.php, CartController.php
  Models/Product.php, Category.php
  Repositories/...
  Services/CartService.php
views/
  cart/index.php
  catalog/...
  layouts/main.php        # счётчик корзины
dataset/
  products.json, categories.json, cart_scenarios.json, catalog_cases.csv
```

## Почему POST для изменений

- GET не должен менять состояние (идемпотентность, кэш, повтор при F5).
- После POST — redirect, поэтому F5 на /cart не дублирует добавление.
