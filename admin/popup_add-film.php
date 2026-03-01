<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Добавление фильма</title>
  <link rel="stylesheet" href="/admin/CSS/normalize.css">
  <link rel="stylesheet" href="/admin/CSS/styles.css">
</head>

<body>
  <div class="popup active">
    <div class="popup__container">
      <div class="popup__content">
        <div class="popup__header">
          <h2 class="popup__title">
            Добавление фильма
            <a class="popup__dismiss" href="#" onclick="closePopup()"><img src="/admin/i/close.png" alt="Закрыть"></a>
          </h2>
        </div>
        <div class="popup__wrapper">
          <form action="/admin.php?action=addFilm" method="post" enctype="multipart/form-data">
            <div class="popup__form">
              <label class="conf-step__label conf-step__label-fullsize">
                <span class="required">Название фильма</span>
                <input class="conf-step__input" type="text" placeholder="Например, «Гражданин Кейн»" name="title" required>
              </label>

              <div class="popup__row">
                <div class="popup__col">
                  <label class="conf-step__label conf-step__label-fullsize">
                    <span class="required">Длительность (мин.)</span>
                    <input class="conf-step__input" type="number" name="duration" min="1" max="300" required>
                  </label>
                </div>
                <div class="popup__col">
                  <label class="conf-step__label conf-step__label-fullsize">
                    Страна
                    <input class="conf-step__input" type="text" name="country" placeholder="Например, США">
                  </label>
                </div>
              </div>

              <label class="conf-step__label conf-step__label-fullsize">
                Описание фильма
                <textarea class="conf-step__input" name="description" placeholder="Краткое описание фильма..." rows="4"></textarea>
              </label>

              <label class="conf-step__label conf-step__label-fullsize">
                Постер
                <input class="conf-step__input" type="file" name="poster" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewPoster(this)">
                <span class="conf-step__hint">Максимальный размер 5MB. Допустимые форматы: JPG, PNG, GIF, WebP</span>
              </label>

              <div id="poster-preview-container"></div>
            </div>

            <div class="conf-step__buttons text-center">
              <input type="submit" value="Добавить фильм" class="conf-step__button conf-step__button-accent">
              <button class="conf-step__button conf-step__button-regular" type="button" onclick="closePopup()">Отменить</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="/admin/js/popup-film.js"></script>
</body>

</html>