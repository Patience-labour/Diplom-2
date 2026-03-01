<?php
require_once __DIR__ . '/core/init.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

$adminActions = ['saveHallConfig', 'savePrices'];
if (in_array($action, $adminActions) && !isAdmin()) {
  http_response_code(403);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

switch ($action) {
  case 'getHallScheme':
    getHallScheme();
    break;

  case 'saveHallConfig':
    saveHallConfig();
    break;

  case 'savePrices':
    savePrices();
    break;

  case 'getSchedule':
    getSchedule();
    break;

  case 'getSession':
    getSession();
    break;

  case 'bookTickets':
    bookTickets();
    break;

  case 'getBooking':
    getBooking();
    break;

  case 'test':
    echo json_encode(['success' => true, 'message' => 'API is working', 'time' => date('Y-m-d H:i:s')]);
    break;

  default:
    http_response_code(404);
    echo json_encode(['error' => 'Action not found']);
}


function getHallScheme()
{
  $hallId = $_GET['hall_id'] ?? 0;
  $rows = $_GET['rows'] ?? 10;
  $cols = $_GET['cols'] ?? 8;

  require_once __DIR__ . '/core/classes/Hall.php';

  $scheme = Hall::getHallScheme($hallId);

  if ($scheme && !empty($scheme['scheme'])) {
    $hallRows = $scheme['rows'];
    $hallCols = $scheme['cols'];
    $seats = $scheme['scheme'];
  } else {
    $hallRows = $rows;
    $hallCols = $cols;
    $seats = [];
  }

  $result = [
    'rows' => $hallRows,
    'cols' => $hallCols,
    'seats' => []
  ];

  for ($row = 1; $row <= $hallRows; $row++) {
    for ($col = 1; $col <= $hallCols; $col++) {
      $seat = $seats[$row][$col] ?? null;
      $type = $seat ? $seat['type'] : 'standart';
      $isActive = $seat ? $seat['is_active'] : true;

      if (!$isActive) $type = 'disabled';

      $result['seats'][] = [
        'row' => $row,
        'col' => $col,
        'type' => $type
      ];
    }
  }

  echo json_encode($result);
}

function saveHallConfig()
{
  $data = json_decode(file_get_contents('php://input'), true);

  if (!$data || !isset($data['hall_id']) || !isset($data['seats'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    return;
  }

  require_once __DIR__ . '/core/classes/Hall.php';

  $hallId = $data['hall_id'];
  $seats = $data['seats'];

  $maxRow = 0;
  $maxCol = 0;
  foreach ($seats as $seat) {
    $maxRow = max($maxRow, $seat['row']);
    $maxCol = max($maxCol, $seat['col']);
  }

  Hall::updateSize($hallId, $maxRow, $maxCol);
  Hall::initSeats($hallId, $maxRow, $maxCol);

  foreach ($seats as $seat) {
    $stmt = DB::getInstance()->prepare("SELECT id FROM seats WHERE hall_id = ? AND `row` = ? AND `col` = ?");
    $stmt->execute([$hallId, $seat['row'], $seat['col']]);
    $seatId = $stmt->fetchColumn();

    if ($seatId) {
      if ($seat['type'] === 'disabled') {
        Hall::updateSeatActive($seatId, false);
      } else {
        Hall::updateSeatActive($seatId, true);
        Hall::updateSeatType($seatId, $seat['type']);
      }
    }
  }

  echo json_encode(['success' => true]);
}

function savePrices()
{
  $data = json_decode(file_get_contents('php://input'), true);

  if (!$data || !isset($data['hall_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    return;
  }

  echo json_encode(['success' => true]);
}

function getSchedule()
{
  $date = $_GET['date'] ?? date('Y-m-d');

  require_once __DIR__ . '/core/classes/Session.php';
  require_once __DIR__ . '/core/classes/Movie.php';
  require_once __DIR__ . '/core/classes/Hall.php';

  $sessions = Session::getByDate($date);

  $movies = [];
  foreach ($sessions as $session) {
    $movieId = $session['movie_id'];

    if (!isset($movies[$movieId])) {
      $movie = Movie::find($movieId);
      $movies[$movieId] = [
        'id' => $movieId,
        'title' => $movie['title'],
        'description' => $movie['description'],
        'duration_min' => $movie['duration_min'],
        'country' => $movie['country'],
        'poster_url' => $movie['poster_url'],
        'halls' => []
      ];
    }

    $hallId = $session['hall_id'];
    if (!isset($movies[$movieId]['halls'][$hallId])) {
      $hall = Hall::find($hallId);
      $movies[$movieId]['halls'][$hallId] = [
        'id' => $hallId,
        'name' => $hall['name'],
        'sessions' => []
      ];
    }

    $movies[$movieId]['halls'][$hallId]['sessions'][] = [
      'id' => $session['id'],
      'start_time' => date('H:i', strtotime($session['start_time']))
    ];
  }

  $result = [];
  foreach ($movies as $movie) {
    $movie['halls'] = array_values($movie['halls']);
    $result[] = $movie;
  }

  echo json_encode(['movies' => $result]);
}

function getSession()
{
  $sessionId = $_GET['id'] ?? 0;

  if (!$sessionId) {
    http_response_code(400);
    echo json_encode(['error' => 'Session ID required']);
    return;
  }

  require_once __DIR__ . '/core/classes/Session.php';
  require_once __DIR__ . '/core/classes/Hall.php';
  require_once __DIR__ . '/core/classes/Ticket.php';

  $session = Session::getDetailed($sessionId);
  if (!$session) {
    http_response_code(404);
    echo json_encode(['error' => 'Session not found']);
    return;
  }

  $hallId = $session['hall_id'];
  $seats = Hall::getSeats($hallId);
  $bookedSeats = Session::getBookedSeats($sessionId);

  $session['start_time'] = date('H:i', strtotime($session['start_time']));

  echo json_encode([
    'id' => $session['id'],
    'movie_title' => $session['movie_title'],
    'hall_name' => $session['hall_name'],
    'start_time' => $session['start_time'],
    'price_standart' => $session['price_standart'],
    'price_vip' => $session['price_vip'],
    'seats' => $seats,
    'booked_seats' => $bookedSeats
  ]);
}

function bookTickets()
{
  $data = json_decode(file_get_contents('php://input'), true);

  if (!$data || !isset($data['session_id']) || !isset($data['seats'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    return;
  }

  $sessionId = $data['session_id'];
  $seatIds = $data['seats'];

  require_once __DIR__ . '/core/classes/Session.php';
  require_once __DIR__ . '/core/classes/Ticket.php';
  require_once __DIR__ . '/core/classes/Hall.php';

  $session = Session::find($sessionId);
  if (!$session) {
    echo json_encode(['success' => false, 'error' => 'Session not found']);
    return;
  }

  $bookedSeats = Session::getBookedSeats($sessionId);
  foreach ($seatIds as $seatId) {
    if (in_array($seatId, $bookedSeats)) {
      echo json_encode(['success' => false, 'error' => 'Some seats are already booked']);
      return;
    }
  }

  $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
  $stmt = DB::getInstance()->prepare("SELECT * FROM seats WHERE id IN ($placeholders)");
  $stmt->execute($seatIds);
  $seats = $stmt->fetchAll();

  $bookingCode = Ticket::generateBookingCode();
  $bookedTickets = [];

  $pdo = DB::getInstance();
  try {
    $pdo->beginTransaction();

    foreach ($seats as $seat) {
      $price = ($seat['type'] === 'vip') ? $session['price_vip'] : $session['price_standart'];

      $ticketId = Ticket::bookTicket(
        $sessionId,
        $seat['id'],
        $price,
        $bookingCode
      );

      if ($ticketId) {
        $bookedTickets[] = [
          'id' => $ticketId,
          'row' => $seat['row'],
          'col' => $seat['col'],
          'price' => $price
        ];
      }
    }

    $pdo->commit();

    echo json_encode([
      'success' => true,
      'booking_code' => $bookingCode,
      'tickets' => $bookedTickets
    ]);
  } catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Booking failed: ' . $e->getMessage()]);
  }
}

function getBooking()
{
  $bookingCode = $_GET['code'] ?? '';

  if (!$bookingCode) {
    http_response_code(400);
    echo json_encode(['error' => 'Booking code required']);
    return;
  }

  require_once __DIR__ . '/core/classes/Ticket.php';

  $tickets = Ticket::findByBookingCode($bookingCode);

  if (empty($tickets)) {
    http_response_code(404);
    echo json_encode(['error' => 'Booking not found']);
    return;
  }

  $booking = [
    'code' => $bookingCode,
    'movie_title' => $tickets[0]['movie_title'],
    'hall_name' => $tickets[0]['hall_name'],
    'start_time' => date('H:i', strtotime($tickets[0]['start_time'])),
    'date' => date('d.m.Y', strtotime($tickets[0]['start_time'])),
    'seats' => [],
    'total_price' => 0
  ];

  foreach ($tickets as $ticket) {
    $booking['seats'][] = [
      'row' => $ticket['seat_row'],
      'col' => $ticket['seat_col'],
      'price' => $ticket['price']
    ];
    $booking['total_price'] += $ticket['price'];
  }

  usort($booking['seats'], function ($a, $b) {
    if ($a['row'] === $b['row']) {
      return $a['col'] - $b['col'];
    }
    return $a['row'] - $b['row'];
  });

  echo json_encode($booking);
}
