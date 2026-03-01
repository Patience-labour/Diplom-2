<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

spl_autoload_register(function ($class) {
  $paths = [
    __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $class . '.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class . '.php',
  ];

  foreach ($paths as $file) {
    if (file_exists($file)) {
      require_once $file;
      return;
    }
  }
});

$functionsFile = __DIR__ . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'functions.php';
if (file_exists($functionsFile)) {
  require_once $functionsFile;
} else {
  die('Файл functions.php не найден по пути: ' . $functionsFile);
}

sessionStart();

if (isset($_GET['debug'])) {
  echo "<pre>";
  echo "ROOT_PATH: " . ROOT_PATH . "\n";
  echo "ADMIN_PATH: " . ADMIN_PATH . "\n";
  echo "CLIENT_PATH: " . CLIENT_PATH . "\n";
  echo "__DIR__: " . __DIR__ . "\n";
  echo "DIRECTORY_SEPARATOR: " . DIRECTORY_SEPARATOR . "\n";
  echo "</pre>";
}
