<?php
require_once __DIR__ . '/Model.php';

class Ticket extends Model
{
  protected static $table = 'tickets';

  public static function bookTicket($sessionId, $seatId, $price, $bookingCode = null)
  {
    if (!$bookingCode) {
      $bookingCode = self::generateBookingCode();
    }
    return self::create([
      'session_id' => $sessionId,
      'seat_id' => $seatId,
      'booking_code' => $bookingCode,
      'price' => $price
    ]);
  }

  public static function generateBookingCode()
  {
    return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
  }

  public static function findByBookingCode($code)
  {
    self::init();

    $sql = "SELECT t.*, 
                       s.start_time, s.hall_id, s.movie_id,
                       m.title as movie_title, m.duration_min,
                       h.name as hall_name,
                       seat.row as seat_row, seat.col as seat_col, seat.type as seat_type
                FROM tickets t
                JOIN sessions s ON t.session_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN halls h ON s.hall_id = h.id
                JOIN seats seat ON t.seat_id = seat.id
                WHERE t.booking_code = ?";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$code]);

    return $stmt->fetchAll();
  }

  public static function isSeatAvailable($sessionId, $seatId)
  {
    self::init();

    $stmt = self::$pdo->prepare("SELECT id FROM " . self::$table . " WHERE session_id = ? AND seat_id = ?");
    $stmt->execute([$sessionId, $seatId]);

    return $stmt->rowCount() === 0;
  }

  public static function getBySession($sessionId)
  {
    self::init();

    $sql = "SELECT t.*, seat.row, seat.col, seat.type
                FROM tickets t
                JOIN seats seat ON t.seat_id = seat.id
                WHERE t.session_id = ?";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$sessionId]);

    return $stmt->fetchAll();
  }

  public static function cancelBooking($ticketId)
  {
    return self::delete($ticketId);
  }

  public static function generateQRCode($bookingCode)
  {
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($bookingCode);
    return $qrCodeUrl;
  }
}
