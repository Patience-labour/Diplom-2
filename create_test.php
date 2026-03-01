<?php
require_once __DIR__ . '/core/init.php';

echo "<h2>Создание тестовых сеансов</h2>";

// Проверяем наличие фильмов
require_once __DIR__ . '/core/classes/Movie.php';
$movies = Movie::all();

if (empty($movies)) {
  echo "❌ Нет фильмов. Сначала создайте фильмы через админку.<br>";
  echo "<a href='/admin.php'>Перейти в админку</a>";
  exit;
}

// Проверяем наличие залов
require_once __DIR__ . '/core/classes/Hall.php';
$halls = Hall::all();

if (empty($halls)) {
  echo "❌ Нет залов. Сначала создайте залы через админку.<br>";
  echo "<a href='/admin.php'>Перейти в админку</a>";
  exit;
}

// Создаем сеансы
require_once __DIR__ . '/core/classes/Session.php';

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$sessions = [
  [
    'movie_id' => $movies[0]['id'],
    'hall_id' => $halls[0]['id'],
    'start_time' => $today . ' 10:00:00',
    'price_standart' => 250,
    'price_vip' => 350
  ],
  [
    'movie_id' => $movies[0]['id'],
    'hall_id' => $halls[0]['id'],
    'start_time' => $today . ' 14:00:00',
    'price_standart' => 300,
    'price_vip' => 400
  ],
  [
    'movie_id' => isset($movies[1]) ? $movies[1]['id'] : $movies[0]['id'],
    'hall_id' => isset($halls[1]) ? $halls[1]['id'] : $halls[0]['id'],
    'start_time' => $today . ' 18:30:00',
    'price_standart' => 280,
    'price_vip' => 380
  ],
  // Завтра
  [
    'movie_id' => $movies[0]['id'],
    'hall_id' => $halls[0]['id'],
    'start_time' => $tomorrow . ' 12:00:00',
    'price_standart' => 250,
    'price_vip' => 350
  ],
  [
    'movie_id' => isset($movies[1]) ? $movies[1]['id'] : $movies[0]['id'],
    'hall_id' => isset($halls[1]) ? $halls[1]['id'] : $halls[0]['id'],
    'start_time' => $tomorrow . ' 20:00:00',
    'price_standart' => 300,
    'price_vip' => 400
  ]
];

foreach ($sessions as $sessionData) {
  $sessionId = Session::createSession($sessionData);
  if ($sessionId) {
    echo "✅ Сеанс создан: " . $sessionData['start_time'] . "<br>";
  } else {
    echo "❌ Ошибка создания сеанса: " . $sessionData['start_time'] . "<br>";
  }
}

echo "<h3>Готово!</h3>";
echo "<a href='/client/index.php'>Перейти на главную клиента</a>";
