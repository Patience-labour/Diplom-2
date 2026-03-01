<?php
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Пароль: " . $password . "<br>";
echo "Хеш: " . $hash . "<br>";
echo "<br>";

echo "SQL запрос для обновления:<br>";
echo "UPDATE admins SET password_hash = '" . $hash . "' WHERE email = 'admin@example.com';";
