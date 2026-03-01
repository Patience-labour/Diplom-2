<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ИдёмВКино - Административная панель</title>
  <link rel="stylesheet" href="/admin/CSS/normalize.css">
  <link rel="stylesheet" href="/admin/CSS/styles.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
</head>

<body>
  <header class="page-header">
    <h1 class="page-header__title">Идём<span>в</span>кино</h1>
    <span class="page-header__subtitle">Администраторррская</span>
    <div class="admin-info">
      <span><?= htmlspecialchars($_SESSION['admin_email'] ?? 'Администратор') ?></span>
      <a href="/admin.php?action=logout">Выйти</a>
    </div>
  </header>

  <main class="conf-steps">
    <?php
    $success = getSuccess();
    $error = getError();
    if ($success):
    ?>
      <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Управление залами -->
    <section class="conf-step">
      <header class="conf-step__header conf-step__header_opened">
        <h2 class="conf-step__title">Управление залами</h2>
      </header>
      <div class="conf-step__wrapper">
        <p class="conf-step__paragraph">Доступные залы:</p>
        <ul class="conf-step__list">
          <?php
          require_once __DIR__ . '/../core/classes/Hall.php';
          $halls = Hall::all();

          if (empty($halls)):
          ?>
            <li class="conf-step__list-empty">Нет созданных залов</li>
            <?php
          else:
            foreach ($halls as $hall):
            ?>
              <li>
                <?= htmlspecialchars($hall['name']) ?>
                <button class="conf-step__button conf-step__button-trash" onclick="deleteHall(<?= $hall['id'] ?>, '<?= htmlspecialchars($hall['name'], ENT_QUOTES) ?>')"></button>
              </li>
          <?php
            endforeach;
          endif;
          ?>
        </ul>
        <button class="conf-step__button conf-step__button-accent" onclick="showAddHallPopup()">Создать зал</button>
      </div>
    </section>

    <!-- Конфигурация залов -->
    <section class="conf-step">
      <header class="conf-step__header conf-step__header_opened">
        <h2 class="conf-step__title">Конфигурация залов</h2>
      </header>
      <div class="conf-step__wrapper">
        <p class="conf-step__paragraph">Выберите зал для конфигурации:</p>

        <?php if (empty($halls)): ?>
          <p class="conf-step__paragraph conf-step__paragraph_warning">Сначала создайте хотя бы один зал</p>
        <?php else: ?>
          <ul class="conf-step__selectors-box">
            <?php foreach ($halls as $index => $hall): ?>
              <li>
                <input type="radio" class="conf-step__radio" name="chairs-hall" value="<?= $hall['id'] ?>" id="hall_<?= $hall['id'] ?>" <?= $index === 0 ? 'checked' : '' ?>>
                <label for="hall_<?= $hall['id'] ?>" class="conf-step__selector"><?= htmlspecialchars($hall['name']) ?></label>
              </li>
            <?php endforeach; ?>
          </ul>

          <p class="conf-step__paragraph">Укажите количество рядов и максимальное количество кресел в ряду:</p>
          <div class="conf-step__legend conf-step__legend_rows">
            <label class="conf-step__label">Рядов, шт
              <input type="number" class="conf-step__input" id="rows" placeholder="10" min="1" max="20" value="10">
            </label>
            <span class="multiplier">x</span>
            <label class="conf-step__label">Мест, шт
              <input type="number" class="conf-step__input" id="cols" placeholder="8" min="1" max="30" value="8">
            </label>
            <button class="conf-step__button conf-step__button-accent" onclick="initHallSeats()">Создать схему</button>
          </div>

          <p class="conf-step__paragraph">Теперь вы можете указать типы кресел на схеме зала:</p>
          <div class="conf-step__legend conf-step__legend_types">
            <span class="conf-step__chair conf-step__chair_standart"></span> — обычные кресла
            <span class="conf-step__chair conf-step__chair_vip"></span> — VIP кресла
            <span class="conf-step__chair conf-step__chair_disabled"></span> — заблокированные (нет кресла)
            <p class="conf-step__hint">Чтобы изменить вид кресла, нажмите по нему левой кнопкой мыши</p>
          </div>

          <div class="conf-step__hall" id="hall-scheme">
            <div class="conf-step__hall-wrapper" id="hall-scheme-content">
              <p class="conf-step__paragraph conf-step__paragraph_center">Выберите зал и укажите размеры, затем нажмите "Создать схему"</p>
            </div>
          </div>

          <fieldset class="conf-step__buttons text-center">
            <button class="conf-step__button conf-step__button-regular" onclick="cancelHallConfig()">Отмена</button>
            <button class="conf-step__button conf-step__button-accent" onclick="saveHallConfig()">Сохранить схему</button>
          </fieldset>
        <?php endif; ?>
      </div>
    </section>

    <!-- Конфигурация цен -->
    <section class="conf-step">
      <header class="conf-step__header conf-step__header_opened">
        <h2 class="conf-step__title">Конфигурация цен</h2>
      </header>
      <div class="conf-step__wrapper">
        <p class="conf-step__paragraph">Выберите зал для конфигурации:</p>

        <?php if (empty($halls)): ?>
          <p class="conf-step__paragraph conf-step__paragraph_warning">Сначала создайте хотя бы один зал</p>
        <?php else: ?>
          <ul class="conf-step__selectors-box">
            <?php foreach ($halls as $index => $hall): ?>
              <li>
                <input type="radio" class="conf-step__radio" name="prices-hall" value="<?= $hall['id'] ?>" id="price_hall_<?= $hall['id'] ?>" <?= $index === 0 ? 'checked' : '' ?>>
                <label for="price_hall_<?= $hall['id'] ?>" class="conf-step__selector"><?= htmlspecialchars($hall['name']) ?></label>
              </li>
            <?php endforeach; ?>
          </ul>

          <p class="conf-step__paragraph">Установите цены для типов кресел:</p>
          <div class="conf-step__legend conf-step__legend_prices">
            <div class="conf-step__legend-item">
              <label class="conf-step__label">Цена, рублей
                <input type="number" class="conf-step__input" id="price_standart" placeholder="0" min="0" value="250">
              </label>
              за <span class="conf-step__chair conf-step__chair_standart"></span> обычные кресла
            </div>
            <div class="conf-step__legend-item">
              <label class="conf-step__label">Цена, рублей
                <input type="number" class="conf-step__input" id="price_vip" placeholder="0" min="0" value="350">
              </label>
              за <span class="conf-step__chair conf-step__chair_vip"></span> VIP кресла
            </div>
          </div>

          <fieldset class="conf-step__buttons text-center">
            <button class="conf-step__button conf-step__button-regular" onclick="resetPrices()">Сбросить</button>
            <button class="conf-step__button conf-step__button-accent" onclick="savePrices()">Сохранить цены</button>
          </fieldset>
        <?php endif; ?>
      </div>
    </section>

    <!-- Управление фильмами -->
    <section class="conf-step">
      <header class="conf-step__header conf-step__header_opened">
        <h2 class="conf-step__title">Управление фильмами</h2>
      </header>
      <div class="conf-step__wrapper">
        <p class="conf-step__paragraph">
          <button class="conf-step__button conf-step__button-accent" onclick="showAddFilmPopup()">+ Добавить фильм</button>
        </p>

        <div class="conf-step__movies" id="movies-list">
          <?php
          require_once __DIR__ . '/../core/classes/Movie.php';
          $movies = Movie::getAllOrdered();

          if (empty($movies)):
          ?>
            <p class="conf-step__paragraph conf-step__paragraph_center" style="grid-column: 1/-1; text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px;">
              Нет добавленных фильмов. Нажмите "Добавить фильм" чтобы создать первый фильм.
            </p>
            <?php
          else:
            foreach ($movies as $movie):
            ?>
              <div class="conf-step__movie" data-movie-id="<?= $movie['id'] ?>">
                <div class="conf-step__movie-poster">
                  <?php if ($movie['poster_url']): ?>
                    <img class="conf-step__movie-poster-image" src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                  <?php else: ?>
                    <div style="color: #999; font-size: 14px;">Нет постера</div>
                  <?php endif; ?>
                </div>
                <div class="conf-step__movie-info">
                  <h3 class="conf-step__movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                  <p style="margin: 5px 0; color: #666; font-size: 14px;"><?= $movie['duration_min'] ?> мин</p>
                  <p style="margin: 5px 0; color: #999; font-size: 13px;"><?= htmlspecialchars($movie['country'] ?? 'Не указана') ?></p>
                </div>
                <div class="conf-step__movie-actions">
                  <button class="conf-step__button conf-step__button-trash" onclick="deleteMovie(<?= $movie['id'] ?>, '<?= htmlspecialchars($movie['title'], ENT_QUOTES) ?>')" style="background: none; border: none; cursor: pointer; opacity: 0.6;">🗑️</button>
                </div>
              </div>
          <?php
            endforeach;
          endif;
          ?>
        </div>
      </div>
    </section>

    <!-- Сетка сеансов -->
    <section class="conf-step">
      <header class="conf-step__header conf-step__header_opened">
        <h2 class="conf-step__title">Сетка сеансов</h2>
      </header>
      <div class="conf-step__wrapper">
        <p class="conf-step__paragraph">
          <button class="conf-step__button conf-step__button-accent" onclick="showAddSeancePopup()">Добавить сеанс</button>
        </p>

        <div class="conf-step__seances" id="seances-grid">
          <?php
          require_once __DIR__ . '/../core/classes/Session.php';
          require_once __DIR__ . '/../core/classes/Movie.php';
          require_once __DIR__ . '/../core/classes/Hall.php';

          $halls = Hall::all();
          $today = date('Y-m-d');
          $sessions = Session::getByDate($today);

          $sessionsByHall = [];
          foreach ($sessions as $session) {
            $sessionsByHall[$session['hall_id']][] = $session;
          }

          if (empty($halls)):
          ?>
            <p class="conf-step__paragraph conf-step__paragraph_center">Сначала создайте хотя бы один зал</p>
          <?php else: ?>
            <div class="conf-step__seances-header">
              <div class="conf-step__seances-date">
                <button class="conf-step__button conf-step__button-regular" onclick="changeDate(-1)">←</button>
                <span id="current-date"><?= date('d.m.Y') ?></span>
                <button class="conf-step__button conf-step__button-regular" onclick="changeDate(1)">→</button>
              </div>
            </div>

            <?php foreach ($halls as $hall): ?>
              <div class="conf-step__seances-hall" data-hall-id="<?= $hall['id'] ?>">
                <h3 class="conf-step__seances-title"><?= htmlspecialchars($hall['name']) ?></h3>
                <div class="conf-step__seances-timeline" id="timeline-hall-<?= $hall['id'] ?>">
                  <?php
                  $hallSessions = $sessionsByHall[$hall['id']] ?? [];
                  usort($hallSessions, function ($a, $b) {
                    return strtotime($a['start_time']) - strtotime($b['start_time']);
                  });
                  $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#D4A5A5', '#9B59B6', '#3498DB'];

                  foreach ($hallSessions as $index => $session):
                    $startTime = strtotime($session['start_time']);
                    $startHour = (int)date('H', $startTime) + (int)date('i', $startTime) / 60;
                    $duration = $session['duration_min'] / 60;
                    $left = ($startHour - 9) * 60;
                    $width = $duration * 60;
                    $color = $colors[$index % count($colors)];
                  ?>
                    <div class="conf-step__seances-movie"
                      style="left: <?= $left ?>px; width: <?= $width ?>px; background-color: <?= $color ?>;"
                      onclick="showSeanceDetails(<?= $session['id'] ?>)"
                      title="<?= htmlspecialchars($session['movie_title']) ?>">
                      <p class="conf-step__seances-movie-title"><?= htmlspecialchars($session['movie_title']) ?></p>
                      <p class="conf-step__seances-movie-start"><?= date('H:i', $startTime) ?></p>
                      <button class="conf-step__seances-movie-delete" onclick="deleteSeance(<?= $session['id'] ?>, event)">×</button>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <fieldset class="conf-step__buttons text-center">
          <button class="conf-step__button conf-step__button-regular" onclick="location.reload()">Обновить</button>
        </fieldset>
      </div>
    </section>
  </main>

  <div id="popup-container"></div>

  <script src="/admin/js/popup-manager.js"></script>
  <script src="/admin/js/accordeon.js"></script>
  <script src="/admin/js/admin.js"></script>
</body>

</html>