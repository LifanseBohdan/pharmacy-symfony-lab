# Аптека — веб-сервіс на Symfony (Лабораторна робота №2)

Веб-застосунок для обліку лікарських засобів на Symfony 7.4 з базою даних MySQL, JSON API, консольною клієнтською програмою на Python, сайтом на Twig та адмін-панеллю EasyAdmin із авторизацією.

## Вимоги

- PHP >= 8.2
- Composer
- MySQL (наприклад, через Laragon)
- Symfony CLI (або вбудований PHP-сервер)
- Python 3.10+ та бібліотека `requests`

## Встановлення та запуск

1. **Встановіть залежності PHP:**
   ```bash
   composer install
   ```

2. **Налаштуйте підключення до бази даних:**
   Створіть файл `.env.local` у корені проєкту (він не комітиться в Git):
   ```dotenv
   DATABASE_URL="mysql://root:@127.0.0.1:3306/pharmacy?serverVersion=8.0.32&charset=utf8mb4"
   ```

3. **Створіть базу даних та застосуйте міграції:**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. **Додайте тестові категорії та препарати:**
   ```bash
   php bin/console app:seed-data
   ```

5. **Створіть адміністратора:**
   Команда інтерактивно запитає email та пароль (введення пароля приховане):
   ```bash
   php bin/console app:admin:create
   ```

6. **Запустіть веб-сервер Symfony:**
   ```bash
   symfony serve
   ```
   (або `php -S 127.0.0.1:8000 -t public`)

7. **Запустіть Python-клієнт:**
   ```bash
   pip install requests
   python client/client.py
   ```
   Для фільтрації лише наявних на складі препаратів:
   ```bash
   python client/client.py --in-stock
   ```

## Основні маршрути

- `GET /medicines` — каталог усіх препаратів (Twig)
- `GET /medicines/{id}` — сторінка окремого препарату з категорією
- `GET /api/medicines` — JSON-список препаратів (опціонально `?inStock=1`)
- `GET /api/medicines/{id}` — JSON-дані одного препарату (або `404`)
- `GET /admin` — адмін-панель EasyAdmin (доступна лише для `ROLE_ADMIN`)
- `GET /login` / `GET /logout` — сторінка входу та виходу
