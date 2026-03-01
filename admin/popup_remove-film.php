<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Удаление фильма</title>
  <link rel="stylesheet" href="/admin/CSS/normalize.css">
  <link rel="stylesheet" href="/admin/CSS/styles.css">

</head>

<body>
  <?php
  $movie = $movie ?? null;
  if (!$movie):
  ?>
    <div class="popup active">
      <div class="popup__container">
        <div class="popup__content">
          <div class="popup__header">
            <h2 class="popup__title">
              Ошибка
              <a class="popup__dismiss" href="#" onclick="PopupManager.close()"><img src="/admin/i/close.png" alt="Закрыть"></a>
            </h2>
          </div>
          <div class="popup__wrapper">
            <p class="conf-step__paragraph">Фильм не найден</p>
            <div class="conf-step__buttons text-center">
              <button class="conf-step__button conf-step__button-regular" type="button" onclick="PopupManager.close()">Закрыть</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="popup active">
      <div class="popup__container">
        <div class="popup__content">
          <div class="popup__header">
            <h2 class="popup__title">
              Удаление фильма
              <a class="popup__dismiss" href="#" onclick="PopupManager.close()"><img src="/admin/i/close.png" alt="Закрыть"></a>
            </h2>
          </div>
          <div class="popup__wrapper">
            <form action="/admin.php?action=removeFilm" method="post" accept-charset="utf-8">
              <input type="hidden" name="id" value="<?= $movie['id'] ?>">

              <p class="conf-step__paragraph">
                Вы действительно хотите удалить фильм <strong>"<?= htmlspecialchars($movie['title']) ?>"</strong>?
              </p>

              <?php if ($movie['poster_url']): ?>
                <div class="conf-step__paragraph" style="text-align: center;">
                  <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="" style="max-width: 150px; max-height: 150px; border-radius: 4px;">
                </div>
              <?php endif; ?>

              <div class="popup__warning">
                <strong>Внимание!</strong> Это действие нельзя отменить. Фильм будет удален из системы, включая все связанные с ним сеансы.
              </div>

              <div class="conf-step__buttons text-center">
                <input type="submit" value="Удалить" class="conf-step__button conf-step__button-accent">
                <button class="conf-step__button conf-step__button-regular" type="button" onclick="PopupManager.close()">Отменить</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</body>

</html>