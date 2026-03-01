let currentDate = new Date();
let selectedDate = formatDate(currentDate);

function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function formatDisplayDate(dateStr) {
  const date = new Date(dateStr + 'T12:00:00');
  const days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
  const months = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

  const dayName = days[date.getDay()];
  const day = date.getDate();
  const month = months[date.getMonth()];

  return { dayName, day, month };
}

function loadDates() {
  const nav = document.getElementById('date-nav');
  let html = '';

  for (let i = 0; i < 7; i++) {
    const date = new Date();
    date.setDate(date.getDate() + i);
    const dateStr = formatDate(date);
    const display = formatDisplayDate(dateStr);

    let classes = 'page-nav__day';
    if (i === 0) classes += ' page-nav__day_today';
    if (dateStr === selectedDate) classes += ' page-nav__day_chosen';
    if (date.getDay() === 0 || date.getDay() === 6) classes += ' page-nav__day_weekend';

    html += `
      <a class="${classes}" href="#" onclick="changeDate('${dateStr}'); return false;">
        <span class="page-nav__day-week">${display.dayName}</span>
        <span class="page-nav__day-number">${display.day}</span>
      </a>
    `;
  }

  nav.innerHTML = html;
}

function changeDate(dateStr) {
  selectedDate = dateStr;
  loadDates();
  loadMovies();
}

function loadMovies() {
  const container = document.getElementById('movies-list');
  container.innerHTML = '<div class="loading">Загрузка расписания...</div>';

  fetch(`/api.php?action=getSchedule&date=${selectedDate}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        container.innerHTML = `<div class="error-message">${data.error}</div>`;
        return;
      }

      if (data.movies.length === 0) {
        container.innerHTML = '<div class="no-sessions">На этот день нет сеансов</div>';
        return;
      }

      renderMovies(data.movies);
    })
    .catch(error => {
      console.error('Error:', error);
      container.innerHTML = '<div class="error-message">Ошибка при загрузке расписания</div>';
    });
}

function renderMovies(movies) {
  const container = document.getElementById('movies-list');
  let html = '';

  movies.forEach(movie => {
    html += `
      <section class="movie">
        <div class="movie__info">
          <div class="movie__poster">
            <img class="movie__poster-image" alt="${escapeHtml(movie.title)}" 
                 src="${movie.poster_url || '/client/i/poster-placeholder.jpg'}">
          </div>
          <div class="movie__description">
            <h2 class="movie__title">${escapeHtml(movie.title)}</h2>
            <p class="movie__synopsis">${escapeHtml(movie.description || 'Нет описания')}</p>
            <p class="movie__data">
              <span class="movie__data-duration">${movie.duration_min} минут</span>
              <span class="movie__data-origin">${escapeHtml(movie.country || 'Не указана')}</span>
            </p>
          </div>
        </div>
        
        ${renderHalls(movie.halls, movie.id)}
      </section>
    `;
  });

  container.innerHTML = html;
}

function renderHalls(halls, movieId) {
  if (!halls || halls.length === 0) return '';

  let html = '';

  halls.forEach(hall => {
    if (!hall.sessions || hall.sessions.length === 0) return;

    html += `
      <div class="movie-seances__hall">
        <h3 class="movie-seances__hall-title">${escapeHtml(hall.name)}</h3>
        <ul class="movie-seances__list">
          ${renderSessions(hall.sessions, movieId, hall.id)}
        </ul>
      </div>
    `;
  });

  return html;
}

function renderSessions(sessions, movieId, hallId) {
  return sessions.map(session => `
    <li class="movie-seances__time-block">
      <a class="movie-seances__time" href="/client/hall.php?session_id=${session.id}">
        ${session.start_time}
      </a>
    </li>
  `).join('');
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('Client JS loaded');
  loadDates();
  loadMovies();
});