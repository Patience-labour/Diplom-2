# 🎬 ИдёмВКино - Система онлайн-бронирования билетов

# 📋 О проекте
## ИдёмВКино - это полнофункциональная система для онлайн-бронирования билетов в кинотеатре. Проект состоит из двух частей:

## 👤 Клиентская часть
* Просмотр расписания сеансов с навигацией по датам

* Интерактивная схема зала с выбором мест

* Бронирование билетов с генерацией уникального кода

* Получение электронного билета с QR-кодом

* Возможность печати билета

## 👨‍💼 Административная часть
* Авторизация администратора

* Управление залами (создание, удаление, настройка схемы)

* Управление фильмами (добавление, удаление, загрузка постеров)

* Настройка цен на билеты

* Создание и управление сеансами

* Визуальная сетка сеансов с навигацией по датам

## СТЕК:

## Backend

- PHP 8.0+ - основной язык разработки
- MySQL - база данных
- PDO - работа с БД (подготовленные запросы, защита от SQL-инъекций)
- MVC архитектура

## Frontend
- HTML5 - разметка
- CSS3 - стилизация (с использованием normalize.css)
- JavaScript (Vanilla) - чистая реализация без фреймворков
- Fetch API - асинхронные запросы
- Google Fonts - шрифт Roboto

## Инструменты
- Git - контроль версий
- Composer - управление зависимостями

# Пошаговая инструкция по развертыванию
1. Клонирование репозитория
``` bash
git clone https://github.com/Patience-labour/Diplom-2.git
cd cinema
```

2. Настройка базы данных
Создайте базу данных MySQL и выполните SQL-дамп:

``` bash
-- Создание базы данных
CREATE DATABASE IF NOT EXISTS cinema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinema_db;

-- Таблица администраторов
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица залов
CREATE TABLE IF NOT EXISTS halls (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    `rows` INT UNSIGNED NOT NULL DEFAULT 0,
    `cols` INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица мест
CREATE TABLE IF NOT EXISTS seats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hall_id INT UNSIGNED NOT NULL,
    `row` INT UNSIGNED NOT NULL,
    `col` INT UNSIGNED NOT NULL,
    type ENUM('standart', 'vip', 'disabled') NOT NULL DEFAULT 'standart',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seat (hall_id, `row`, `col`)
);

-- Таблица фильмов
CREATE TABLE IF NOT EXISTS movies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration_min INT UNSIGNED NOT NULL,
    country VARCHAR(100),
    poster_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица сеансов
CREATE TABLE IF NOT EXISTS sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id INT UNSIGNED NOT NULL,
    hall_id INT UNSIGNED NOT NULL,
    start_time DATETIME NOT NULL,
    price_standart INT UNSIGNED NOT NULL DEFAULT 0,
    price_vip INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
);

-- Таблица билетов
CREATE TABLE IF NOT EXISTS tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id INT UNSIGNED NOT NULL,
    seat_id INT UNSIGNED NOT NULL,
    booking_code VARCHAR(100) NOT NULL UNIQUE,
    price INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
    UNIQUE KEY unique_session_seat (session_id, seat_id)
);

-- Добавление тестового администратора (пароль: 123)
-- Сгенерируйте свой хеш через password_hash('123', PASSWORD_DEFAULT)
INSERT INTO admins (email, password_hash) VALUES 
('admin@example.com', '$2y$10$YourGeneratedHashHere');
```

3. Настройка конфигурации
Отредактируйте файл core/config/database.php:

``` bash
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cinema_db');
define('DB_USER', 'root');      // Ваш пользователь MySQL
define('DB_PASS', '');          // Ваш пароль MySQL (123)

define('DEBUG_MODE', true);      // Режим отладки

define('ROOT_PATH', dirname(__DIR__, 2));
define('ADMIN_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'admin');
define('CLIENT_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'client');
```

4. Создание тестового администратора
Создайте файл generate_hash.php для генерации хеша пароля:

``` bash
php
<?php
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Пароль: $password<br>";
echo "Хеш: $hash<br>";
echo "SQL запрос: INSERT INTO admins (email, password_hash) VALUES ('admin@example.com', '$hash');";
```

Запустите его и выполните полученный SQL запрос.

5. Настройка веб-сервера

* Для OpenServer: Разместите проект в папке domains/cinema

* Убедитесь, что используется PHP 8.0+

* Проверьте настройки Apache/Nginx

Для встроенного PHP-сервера:
``` bash
cd \ваш путь до папки\layouts\cinema
php -S localhost:8000
```

6. Проверка работоспособности
* Откройте в браузере http://localhost:8000/client/index.php - клиентская часть

*Откройте http://localhost:8000/admin.php - админ-панель

*Войдите с учетными данными: admin@example.com / 123
