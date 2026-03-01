<?php
require_once __DIR__ . '/Model.php';

class Hall extends Model
{
  protected static $table = 'halls';

  public static function createHall($name)
  {
    return self::create([
      'name' => $name,
      'rows' => 0,
      'cols' => 0
    ]);
  }

  public static function updateSize($id, $rows, $cols)
  {
    return self::update($id, [
      'rows' => $rows,
      'cols' => $cols
    ]);
  }

  public static function getSeats($hallId)
  {
    self::init();
    $stmt = self::$pdo->prepare("SELECT * FROM seats WHERE hall_id = ? ORDER BY `row`, `col`");
    $stmt->execute([$hallId]);
    return $stmt->fetchAll();
  }

  public static function initSeats($hallId, $rows, $cols)
  {
    self::init();

    $stmt = self::$pdo->prepare("DELETE FROM seats WHERE hall_id = ?");
    $stmt->execute([$hallId]);

    $sql = "INSERT INTO seats (hall_id, `row`, `col`, type, is_active) VALUES (?, ?, ?, 'standart', 1)";
    $stmt = self::$pdo->prepare($sql);

    for ($row = 1; $row <= $rows; $row++) {
      for ($col = 1; $col <= $cols; $col++) {
        $stmt->execute([$hallId, $row, $col]);
      }
    }

    return true;
  }


  public static function updateSeatType($seatId, $type)
  {
    self::init();
    $stmt = self::$pdo->prepare("UPDATE seats SET type = ? WHERE id = ?");
    return $stmt->execute([$type, $seatId]);
  }


  public static function updateSeatActive($seatId, $isActive)
  {
    self::init();
    $stmt = self::$pdo->prepare("UPDATE seats SET is_active = ? WHERE id = ?");
    return $stmt->execute([$isActive, $seatId]);
  }

  public static function getHallScheme($hallId)
  {
    $hall = self::find($hallId);
    if (!$hall) {
      return null;
    }

    $seats = self::getSeats($hallId);

    $scheme = [];
    foreach ($seats as $seat) {
      $scheme[$seat['row']][$seat['col']] = $seat;
    }

    return [
      'hall' => $hall,
      'scheme' => $scheme,
      'rows' => $hall['rows'],
      'cols' => $hall['cols']
    ];
  }
}
