<?php
require_once __DIR__ . '/core/init.php';

$email = 'admin@example.com';
$password = '123';

echo "<h2>Тест авторизации</h2>";

$admin = Admin::findByEmail($email);

if ($admin) {
  echo "✅ Администратор найден: " . $admin['email'] . "<br>";
  echo "Хеш в БД: " . $admin['password_hash'] . "<br>";

  if (password_verify($password, $admin['password_hash'])) {
    echo "✅ Пароль верный! Авторизация успешна.<br>";
  } else {
    echo "❌ Пароль неверный!<br>";

    $testHash = password_hash($password, PASSWORD_DEFAULT);
    echo "Хеш для пароля '123': " . $testHash . "<br>";
  }
} else {
  echo "❌ Администратор не найден!<br>";
}

echo "<br><br>";
echo "<a href='/admin.php?action=login'>Перейти на страницу входа</a>";
