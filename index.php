<?php
require_once __DIR__ . '/core/init.php';

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$controller = new ClientController();

if (method_exists($controller, $action)) {
  if ($id) {
    $controller->$action($id);
  } else {
    $controller->$action();
  }
} else {
  $controller->index();
}
