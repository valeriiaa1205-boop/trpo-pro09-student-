# Чек-лист проверки ПР-08

- [x] Проект запускается по README (`php -S localhost:8080 -t public`)
- [x] dataset/products.json и categories.json реально используются
- [x] ProductRepository читает JSON и возвращает Product[]
- [x] CatalogController получает репозиторий через конструктор
- [x] View не открывает файлы, только выводит модели
- [x] Фильтрация по категории (`?category=`)
- [x] Сортировка по цене/названию (`?sort=`)
- [x] Карточка товара `/catalog/{id}`
- [x] 404 при неизвестном id
- [x] Пустое состояние / неизвестная категория
- [x] Форматирование цены (метод модели)
- [x] Namespaces + spl_autoload_register
- [x] README со схемой MVC
- [x] .gitignore
- [x] Сценарии catalog_cases.csv проверены
