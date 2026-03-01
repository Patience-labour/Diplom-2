<?php
require_once __DIR__ . '/Model.php';

class Admin extends Model
{
  protected static $table = 'admins';

  public static function findByEmail($email)
  {
    self::init();
    $stmt = self::$pdo->prepare("SELECT * FROM " . self::$table . " WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
  }

  public static function hashPassword($password)
  {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  public static function verifyPassword($password, $hash)
  {
    return password_verify($password, $hash);
  }
}
