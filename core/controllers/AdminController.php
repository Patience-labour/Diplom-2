<?php
class AdminController
{
  public function index()
  {
    $this->checkAuth();
    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'index.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл index.php не найден в папке admin: ' . $file);
    }
  }

  public function login()
  {
    if (isAdmin()) {
      redirect(adminUrl('index'));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';

      $adminFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Admin.php';

      if (!file_exists($adminFile)) {
        die("Файл Admin.php не найден по пути: " . $adminFile);
      }

      require_once $adminFile;

      $admin = Admin::findByEmail($email);

      if ($admin && Admin::verifyPassword($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        setSuccess('Добро пожаловать в админ-панель!');
        redirect(adminUrl('index'));
      } else {
        setError('Неверный email или пароль');
      }
    }

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'login.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл login.php не найден в папке admin: ' . $file);
    }
  }

  public function logout()
  {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_email']);
    setSuccess('Вы вышли из системы');
    redirect(adminUrl('login'));
  }

  public function addHall()
  {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = $_POST['name'] ?? '';
      if ($name) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Hall.php';

        $hallId = Hall::createHall($name);

        if ($hallId) {
          setSuccess('Зал "' . htmlspecialchars($name) . '" успешно добавлен');
        } else {
          setError('Ошибка при создании зала');
        }
        redirect(adminUrl('index'));
      } else {
        setError('Название зала не может быть пустым');
      }
    }

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'popup_add-hall.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл popup_add-hall.php не найден в папке admin');
    }
  }

  public function removeHall()
  {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? 0;

      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Hall.php';

      if (Hall::delete($id)) {
        setSuccess('Зал успешно удален');
      } else {
        setError('Ошибка при удалении зала');
      }
      redirect(adminUrl('index'));
    }

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'popup_remove-hall.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл popup_remove-hall.php не найден');
    }
  }

  public function addFilm()
  {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Movie.php';

      $data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'duration' => $_POST['duration'] ?? 0,
        'country' => $_POST['country'] ?? ''
      ];

      $errors = [];
      if (empty($data['title'])) {
        $errors[] = 'Название фильма обязательно';
      }
      if (empty($data['duration']) || !is_numeric($data['duration']) || $data['duration'] <= 0) {
        $errors[] = 'Продолжительность должна быть положительным числом';
      }

      if (!empty($errors)) {
        setError(implode('<br>', $errors));
        redirect(adminUrl('index'));
      }

      if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $posterUrl = Movie::uploadPoster($_FILES['poster']);
        if ($posterUrl) {
          $data['poster_url'] = $posterUrl;
        }
      }

      $movieId = Movie::createMovie($data);

      if ($movieId) {
        setSuccess('Фильм "' . htmlspecialchars($data['title']) . '" успешно добавлен');
      } else {
        setError('Ошибка при добавлении фильма');
      }
      redirect(adminUrl('index'));
    }

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'popup_add-film.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл popup_add-film.php не найден');
    }
  }

  public function removeFilm()
  {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? 0;

      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Movie.php';

      if (Movie::deleteWithPoster($id)) {
        if (
          !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
          header('Content-Type: application/json');
          echo json_encode(['success' => true, 'message' => 'Фильм успешно удален']);
          exit;
        }

        setSuccess('Фильм успешно удален');
      } else {
        if (
          !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
          header('Content-Type: application/json');
          http_response_code(400);
          echo json_encode(['success' => false, 'error' => 'Ошибка при удалении фильма']);
          exit;
        }

        setError('Ошибка при удалении фильма');
      }

      redirect(adminUrl('index'));
    }

    $id = $_GET['id'] ?? 0;
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Movie.php';
    $movie = Movie::find($id);

    if (!$movie) {
      setError('Фильм не найден');
      redirect(adminUrl('index'));
    }

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'popup_remove-film.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл popup_remove-film.php не найден');
    }
  }


  public function addSeance()
  {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Session.php';
      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Movie.php';
      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Hall.php';

      $hallId = $_POST['hall_id'] ?? 0;
      $movieId = $_POST['movie_id'] ?? 0;
      $date = $_POST['date'] ?? date('Y-m-d');
      $time = $_POST['time'] ?? '09:00';
      $priceStandart = $_POST['price_standart'] ?? 0;
      $priceVip = $_POST['price_vip'] ?? 0;

      $startTime = $date . ' ' . $time . ':00';

      $errors = [];

      if (!$hallId) $errors[] = 'Выберите зал';
      if (!$movieId) $errors[] = 'Выберите фильм';

      $movie = Movie::find($movieId);
      if (!$movie) $errors[] = 'Фильм не найден';

      $hall = Hall::find($hallId);
      if (!$hall) $errors[] = 'Зал не найден';

      if (empty($errors)) {
        if (Session::canAddSession($hallId, $startTime, $movie['duration_min'])) {
          $sessionId = Session::createSession([
            'movie_id' => $movieId,
            'hall_id' => $hallId,
            'start_time' => $startTime,
            'price_standart' => $priceStandart,
            'price_vip' => $priceVip
          ]);

          if ($sessionId) {
            setSuccess('Сеанс успешно добавлен');
          } else {
            setError('Ошибка при добавлении сеанса');
          }
        } else {
          setError('Это время уже занято другим сеансом');
        }
      } else {
        setError(implode('<br>', $errors));
      }

      redirect(adminUrl('index'));
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Hall.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Movie.php';

    $halls = Hall::all();
    $movies = Movie::getAllOrdered();

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'popup_add-seance.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл popup_add-seance.php не найден');
    }
  }

  public function removeSeance()
  {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'] ?? 0;

      require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Session.php';

      if (Session::deleteWithTickets($id)) {
        setSuccess('Сеанс успешно удален');
      } else {
        setError('Ошибка при удалении сеанса');
      }
      redirect(adminUrl('index'));
    }

    $id = $_GET['id'] ?? 0;
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Session.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Movie.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Hall.php';

    $session = Session::getDetailed($id);

    if (!$session) {
      setError('Сеанс не найден');
      redirect(adminUrl('index'));
    }

    $file = ADMIN_PATH . DIRECTORY_SEPARATOR . 'popup_remove-seance.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл popup_remove-seance.php не найден');
    }
  }

  public function getSessionsByDate()
  {
    $this->checkAuth();

    $date = $_GET['date'] ?? date('Y-m-d');

    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Session.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Hall.php';

    $sessions = Session::getByDate($date);
    $halls = Hall::all();

    header('Content-Type: application/json');
    echo json_encode([
      'sessions' => $sessions,
      'halls' => $halls,
      'date' => $date
    ]);
  }

  private function checkAuth()
  {
    if (!isAdmin()) {
      setError('Необходимо авторизоваться');
      redirect(adminUrl('login'));
    }
  }
}
