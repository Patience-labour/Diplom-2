<?php
require_once __DIR__ . '/core/init.php';

$action = $_GET['action'] ?? 'index';

$controller = new AdminController();

if (method_exists($controller, $action)) {
  $controller->$action();
} else {
  $controller->index();
}
