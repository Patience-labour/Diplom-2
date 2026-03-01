<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Добавление зала</title>
  <link rel="stylesheet" href="/admin/CSS/normalize.css">
  <link rel="stylesheet" href="/admin/CSS/styles.css">
</head>

<body>
  <div class="popup active">
    <div class="popup__container">
      <div class="popup__content">
        <div class="popup__header">
          <h2 class="popup__title">
            Добавление зала
            <a class="popup__dismiss" href="#" onclick="closePopup()"><img src="/admin/i/close.png" alt="Закрыть"></a>
          </h2>
        </div>
        <div class="popup__wrapper">
          <form action="/admin.php?action=addHall" method="post">
            <label class="conf-step__label conf-step__label-fullsize" for="name">
              Название зала
              <input class="conf-step__input" type="text" placeholder="Например, «Зал 1»" name="name" id="name" required>
            </label>
            <div class="conf-step__buttons text-center">
              <input type="submit" value="Добавить зал" class="conf-step__button conf-step__button-accent">
              <button class="conf-step__button conf-step__button-regular" type="button" onclick="closePopup()">Отменить</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="/admin/js/popup-hall.js"></script>
</body>

</html>