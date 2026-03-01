<?php
require_once __DIR__ . '/Model.php';

class Movie extends Model
{
  protected static $table = 'movies';

  public static function createMovie($data)
  {
    $movieData = [
      'title' => $data['title'],
      'description' => $data['description'] ?? '',
      'duration_min' => $data['duration'],
      'country' => $data['country'] ?? '',
      'poster_url' => $data['poster_url'] ?? null
    ];

    return self::create($movieData);
  }

  public static function updateMovie($id, $data)
  {
    $movieData = [];
    if (isset($data['title'])) $movieData['title'] = $data['title'];
    if (isset($data['description'])) $movieData['description'] = $data['description'];
    if (isset($data['duration'])) $movieData['duration_min'] = $data['duration'];
    if (isset($data['country'])) $movieData['country'] = $data['country'];
    if (isset($data['poster_url'])) $movieData['poster_url'] = $data['poster_url'];

    return self::update($id, $movieData);
  }

  public static function updatePoster($id, $posterUrl)
  {
    return self::update($id, ['poster_url' => $posterUrl]);
  }

  public static function searchByTitle($title)
  {
    self::init();
    $stmt = self::$pdo->prepare("SELECT * FROM " . self::$table . " WHERE title LIKE ? ORDER BY title");
    $stmt->execute(['%' . $title . '%']);
    return $stmt->fetchAll();
  }

  public static function getAllOrdered()
  {
    self::init();
    $stmt = self::$pdo->query("SELECT * FROM " . self::$table . " ORDER BY title");
    return $stmt->fetchAll();
  }

  public static function uploadPoster($file)
  {
    $targetDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'posters' . DIRECTORY_SEPARATOR;

    if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . uniqid() . '.' . $extension;
    $targetFile = $targetDir . $fileName;

    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
      return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
      return false;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($check['mime'], $allowedTypes)) {
      return false;
    }

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
      return '/uploads/posters/' . $fileName;
    }

    return false;
  }

  public static function deleteWithPoster($id)
  {
    $movie = self::find($id);
    if ($movie && $movie['poster_url']) {
      $posterPath = ROOT_PATH . $movie['poster_url'];
      if (file_exists($posterPath)) {
        unlink($posterPath);
      }
    }

    return self::delete($id);
  }
}
