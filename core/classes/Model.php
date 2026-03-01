<?php
abstract class Model
{
  protected static $table;
  protected static $pdo;

  public static function init()
  {
    self::$pdo = DB::getInstance();
  }

  public static function all()
  {
    self::init();
    $stmt = self::$pdo->query("SELECT * FROM " . static::$table);
    return $stmt->fetchAll();
  }

  public static function find($id)
  {
    self::init();
    $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function where($field, $value)
  {
    self::init();
    $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table . " WHERE $field = ?");
    $stmt->execute([$value]);
    return $stmt->fetchAll();
  }

  public static function create($data)
  {
    self::init();

    $fields = array_map(function ($field) {
      return "`$field`";
    }, array_keys($data));

    $fieldsStr = implode(', ', $fields);
    $placeholders = implode(', ', array_fill(0, count($data), '?'));

    $sql = "INSERT INTO " . static::$table . " ($fieldsStr) VALUES ($placeholders)";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute(array_values($data));

    return self::$pdo->lastInsertId();
  }

  public static function update($id, $data)
  {
    self::init();

    $setParts = [];
    foreach (array_keys($data) as $field) {
      $setParts[] = "`$field` = ?";
    }
    $set = implode(', ', $setParts);

    $sql = "UPDATE " . static::$table . " SET $set WHERE id = ?";

    $values = array_values($data);
    $values[] = $id;

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($values);

    return $stmt->rowCount();
  }

  public static function delete($id)
  {
    self::init();
    $stmt = self::$pdo->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
    $stmt->execute([$id]);

    return $stmt->rowCount();
  }
}
