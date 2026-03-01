<?php
function redirect($url)
{
  header("Location: $url");
  exit;
}

function adminUrl($action = 'index')
{
  return "/admin.php?action=" . urlencode($action);
}

function clientUrl($action = 'index', $id = null)
{
  $url = "/index.php?action=" . urlencode($action);
  if ($id !== null) {
    $url .= "&id=" . urlencode($id);
  }
  return $url;
}

function sessionStart()
{
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
}

function isAdmin()
{
  return isset($_SESSION['admin_id']);
}

function debug($data)
{
  if (defined('DEBUG_MODE') && DEBUG_MODE) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }
}

function getError()
{
  $error = $_SESSION['error'] ?? '';
  unset($_SESSION['error']);
  return $error;
}

function getSuccess()
{
  $success = $_SESSION['success'] ?? '';
  unset($_SESSION['success']);
  return $success;
}

function setError($message)
{
  $_SESSION['error'] = $message;
}

function setSuccess($message)
{
  $_SESSION['success'] = $message;
}

function loadClass($className)
{
  $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $className . '.php';
  if (file_exists($file)) {
    require_once $file;
    return true;
  }
  return false;
}
