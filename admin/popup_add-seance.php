<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ИдёмВКино</title>
  <link rel="stylesheet" href="CSS/normalize.css">
  <link rel="stylesheet" href="CSS/styles.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
</head>

<body>

  <div class="popup active">
    <div class="popup__container">
      <div class="popup__content">
        <div class="popup__header">
          <h2 class="popup__title">
            Добавление сеанса
            <a class="popup__dismiss" href="#" onclick="closePopup()"><img src="/admin/i/close.png" alt="Закрыть"></a>
          </h2>
        </div>
        <div class="popup__wrapper">
          <form action="/admin.php?action=addSeance" method="post" accept-charset="utf-8">
            <div class="popup__row">
              <div class="popup__col">
                <label class="conf-step__label required" for="hall_id">
                  Выберите зал
                  <select name="hall_id" id="hall_id" required>
                    <option value="">-- Выберите зал --</option>
                    <?php foreach ($halls as $hall): ?>
                      <option value="<?= $hall['id'] ?>"><?= htmlspecialchars($hall['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
              </div>
              <div class="popup__col">
                <label class="conf-step__label required" for="movie_id">
                  Выберите фильм
                  <select name="movie_id" id="movie_id" required>
                    <option value="">-- Выберите фильм --</option>
                    <?php foreach ($movies as $movie): ?>
                      <option value="<?= $movie['id'] ?>"
                        data-duration="<?= $movie['duration_min'] ?>">
                        <?= htmlspecialchars($movie['title']) ?> (<?= $movie['duration_min'] ?> мин)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </label>
              </div>
            </div>

            <div class="popup__row">
              <div class="popup__col">
                <label class="conf-step__label required" for="date">
                  Дата
                  <input class="conf-step__input" type="date" name="date" id="date"
                    value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                </label>
              </div>
              <div class="popup__col">
                <label class="conf-step__label required" for="time">
                  Время начала
                  <input class="conf-step__input" type="time" name="time" id="time" value="09:00" required>
                </label>
              </div>
            </div>

            <div class="price-info" id="movie-info">
              <p style="color: #666; font-style: italic;">Выберите фильм для просмотра информации</p>
            </div>

            <div class="popup__row">
              <div class="popup__col">
                <label class="conf-step__label required" for="price_standart">
                  Цена обычных мест (₽)
                  <input class="conf-step__input" type="number" name="price_standart" id="price_standart"
                    value="250" min="0" step="10" required>
                </label>
              </div>
              <div class="popup__col">
                <label class="conf-step__label required" for="price_vip">
                  Цена VIP мест (₽)
                  <input class="conf-step__input" type="number" name="price_vip" id="price_vip"
                    value="350" min="0" step="10" required>
                </label>
              </div>
            </div>

            <div class="conf-step__buttons text-center">
              <input type="submit" value="Добавить сеанс" class="conf-step__button conf-step__button-accent">
              <button class="conf-step__button conf-step__button-regular" type="button" onclick="closePopup()">Отменить</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="/admin/js/popup-seance.js"></script>
</body>
</html>