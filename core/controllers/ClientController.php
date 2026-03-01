<?php
class ClientController
{
  public function index()
  {
    $file = CLIENT_PATH . DIRECTORY_SEPARATOR . 'index.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл index.php не найден в папке client: ' . $file);
    }
  }

  public function hall($id = null)
  {
    if (!$id) {
      redirect(clientUrl('index'));
    }

    $file = CLIENT_PATH . DIRECTORY_SEPARATOR . 'hall.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл hall.php не найден в папке client: ' . $file);
    }
  }

  public function payment()
  {
    $file = CLIENT_PATH . DIRECTORY_SEPARATOR . 'payment.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл payment.php не найден в папке client: ' . $file);
    }
  }

  public function ticket($code = null)
  {
    $file = CLIENT_PATH . DIRECTORY_SEPARATOR . 'ticket.php';
    if (file_exists($file)) {
      include $file;
    } else {
      die('Файл ticket.php не найден в папке client: ' . $file);
    }
  }
}
