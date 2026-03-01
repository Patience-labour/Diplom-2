<?php
require_once __DIR__ . '/Model.php';

class Session extends Model
{
  protected static $table = 'sessions';

  public static function createSession($data)
  {
    $sessionData = [
      'movie_id' => $data['movie_id'],
      'hall_id' => $data['hall_id'],
      'start_time' => $data['start_time'],
      'price_standart' => $data['price_standart'] ?? 0,
      'price_vip' => $data['price_vip'] ?? 0
    ];

    return self::create($sessionData);
  }

  public static function getByDate($date)
  {
    self::init();

    $startOfDay = $date . ' 00:00:00';
    $endOfDay = $date . ' 23:59:59';

    $sql = "SELECT s.*, m.title as movie_title, m.duration_min, m.poster_url,
                       h.name as hall_name, h.rows, h.cols
                FROM sessions s
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                WHERE s.start_time BETWEEN ? AND ?
                ORDER BY s.start_time";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$startOfDay, $endOfDay]);

    return $stmt->fetchAll();
  }

  public static function getByMovie($movieId)
  {
    self::init();

    $sql = "SELECT s.*, h.name as hall_name
                FROM sessions s
                JOIN halls h ON s.hall_id = h.id
                WHERE s.movie_id = ?
                ORDER BY s.start_time";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$movieId]);

    return $stmt->fetchAll();
  }

  public static function getDetailed($id)
  {
    self::init();

    $sql = "SELECT s.*, 
                       m.title as movie_title, m.description, m.duration_min, m.country, m.poster_url,
                       h.name as hall_name, h.rows, h.cols
                FROM sessions s
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                WHERE s.id = ?";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch();
  }

  public static function canAddSession($hallId, $startTime, $duration)
  {
    self::init();

    $endTime = date('Y-m-d H:i:s', strtotime($startTime . " + $duration minutes"));

    $sql = "SELECT s.*, m.duration_min
                FROM sessions s
                JOIN movies m ON s.movie_id = m.id
                WHERE s.hall_id = ?
                AND s.start_time BETWEEN ? AND ?
                OR DATE_ADD(s.start_time, INTERVAL m.duration_min MINUTE) BETWEEN ? AND ?";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$hallId, $startTime, $endTime, $startTime, $endTime]);

    return $stmt->rowCount() === 0;
  }

  public static function getBookedSeats($sessionId)
  {
    self::init();

    $sql = "SELECT seat_id FROM tickets WHERE session_id = ?";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$sessionId]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }


  public static function deleteWithTickets($id)
  {
    self::init();

    try {
      self::$pdo->beginTransaction();

      $stmt = self::$pdo->prepare("DELETE FROM tickets WHERE session_id = ?");
      $stmt->execute([$id]);

      $stmt = self::$pdo->prepare("DELETE FROM " . self::$table . " WHERE id = ?");
      $stmt->execute([$id]);

      self::$pdo->commit();
      return true;
    } catch (Exception $e) {
      self::$pdo->rollBack();
      return false;
    }
  }
}
