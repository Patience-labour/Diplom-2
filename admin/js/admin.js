function showAddHallPopup() {
  console.log('showAddHallPopup called');
  PopupManager.load('/admin.php?action=addHall');
}

function deleteHall(id, name) {
  console.log('deleteHall called', id, name);
  if (confirm(`Удалить зал "${name}"?`)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin.php?action=removeHall';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = id;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}

function initHallSeats() {
  console.log('initHallSeats called');
  const hallId = document.querySelector('input[name="chairs-hall"]:checked')?.value;
  const rows = document.getElementById('rows')?.value;
  const cols = document.getElementById('cols')?.value;

  if (!hallId) {
    alert('Выберите зал');
    return;
  }

  if (!rows || !cols) {
    alert('Укажите количество рядов и мест');
    return;
  }

  fetch(`/api.php?action=getHallScheme&hall_id=${hallId}&rows=${rows}&cols=${cols}`)
    .then(response => response.json())
    .then(data => {
      const html = renderHallScheme(data);
      document.getElementById('hall-scheme-content').innerHTML = html;
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Ошибка при загрузке схемы зала');
    });
}

function cancelHallConfig() {
  console.log('cancelHallConfig called');
  document.getElementById('hall-scheme-content').innerHTML = '<p class="conf-step__paragraph conf-step__paragraph_center">Выберите зал и укажите размеры, затем нажмите "Создать схему"</p>';
}

function saveHallConfig() {
  console.log('saveHallConfig called');
  const seats = [];
  document.querySelectorAll('.conf-step__row .conf-step__chair').forEach((chair) => {
    seats.push({
      row: chair.dataset.row,
      col: chair.dataset.col,
      type: chair.classList.contains('conf-step__chair_vip') ? 'vip' :
        chair.classList.contains('conf-step__chair_disabled') ? 'disabled' : 'standart'
    });
  });

  const hallId = document.querySelector('input[name="chairs-hall"]:checked')?.value;

  if (!hallId) {
    alert('Выберите зал');
    return;
  }

  if (seats.length === 0) {
    alert('Сначала создайте схему зала');
    return;
  }

  fetch('/api.php?action=saveHallConfig', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      hall_id: hallId,
      seats: seats
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Конфигурация сохранена');
      } else {
        alert('Ошибка при сохранении: ' + (data.error || 'неизвестная ошибка'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Ошибка при сохранении');
    });
}

function renderHallScheme(data) {
  console.log('Rendering hall scheme:', data);

  let html = '<div class="conf-step__hall-wrapper">';

  for (let row = 1; row <= data.rows; row++) {
    html += '<div class="conf-step__row">';

    for (let col = 1; col <= data.cols; col++) {
      const seat = data.seats.find(s => parseInt(s.row) === row && parseInt(s.col) === col);

      let seatType = 'standart';
      if (seat) {
        seatType = seat.type;
      }

      html += `<span class="conf-step__chair conf-step__chair_${seatType}" 
                    data-row="${row}" 
                    data-col="${col}"
                    onclick="toggleChairType(this)"></span>`;
    }

    html += '</div>';
  }

  html += '</div>';

  console.log('Generated HTML:', html);
  return html;
}

window.toggleChairType = function (el) {
  if (el.classList.contains('conf-step__chair_standart')) {
    el.classList.remove('conf-step__chair_standart');
    el.classList.add('conf-step__chair_vip');
  } else if (el.classList.contains('conf-step__chair_vip')) {
    el.classList.remove('conf-step__chair_vip');
    el.classList.add('conf-step__chair_disabled');
  } else {
    el.classList.remove('conf-step__chair_disabled');
    el.classList.add('conf-step__chair_standart');
  }
};

function showAddFilmPopup() {
  console.log('showAddFilmPopup called');
  PopupManager.load('/admin.php?action=addFilm');
}

function deleteMovie(id, title) {
  console.log('deleteMovie called', id, title);
  if (confirm(`Удалить фильм "${title}"?`)) {
    const url = `/admin.php?action=removeFilm&id=${id}`;
    console.log('Loading URL:', url);

    // Используем fetch для отправки запроса на удаление
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id=${id}`
    })
      .then(response => {
        if (response.redirected) {
          window.location.reload();
        } else {
          return response.text();
        }
      })
      .then(() => {
        const movieElement = document.querySelector(`.conf-step__movie[data-movie-id="${id}"]`);
        if (movieElement) {
          movieElement.remove();

          const moviesContainer = document.getElementById('movies-list');
          const remainingMovies = moviesContainer.querySelectorAll('.conf-step__movie');

          if (remainingMovies.length === 0) {
            moviesContainer.innerHTML = '<p class="conf-step__paragraph conf-step__paragraph_center" style="grid-column: 1/-1; text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px;">Нет добавленных фильмов. Нажмите "Добавить фильм" чтобы создать первый фильм.</p>';
          }
        }

        alert('Фильм успешно удален');
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при удалении фильма');
      });
  }
}

function resetPrices() {
  console.log('resetPrices called');
  document.getElementById('price_standart').value = '250';
  document.getElementById('price_vip').value = '350';
}

function savePrices() {
  console.log('savePrices called');
  const hallId = document.querySelector('input[name="prices-hall"]:checked')?.value;
  const priceStandart = document.getElementById('price_standart').value;
  const priceVip = document.getElementById('price_vip').value;

  if (!hallId) {
    alert('Выберите зал');
    return;
  }

  fetch('/api.php?action=savePrices', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      hall_id: hallId,
      price_standart: priceStandart,
      price_vip: priceVip
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Цены сохранены');
      } else {
        alert('Ошибка при сохранении');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Ошибка при сохранении');
    });
}

function previewPoster(input) {
  console.log('previewPoster called');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById('poster-preview');
      if (!preview) {
        const img = document.createElement('img');
        img.id = 'poster-preview';
        img.style.maxWidth = '200px';
        img.style.maxHeight = '200px';
        img.style.marginTop = '10px';
        img.style.border = '1px solid #ddd';
        img.style.borderRadius = '4px';
        img.style.padding = '5px';
        input.parentNode.appendChild(img);
      }
      document.getElementById('poster-preview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function showAddSeancePopup() {
  console.log('showAddSeancePopup called');
  PopupManager.load('/admin.php?action=addSeance');
}

function showSeanceDetails(id) {
  console.log('Seance details:', id);
  alert('Просмотр деталей сеанса ' + id + ' (в разработке)');
}

function deleteSeance(id, event) {
  console.log('deleteSeance called', id);
  if (event) {
    event.stopPropagation();
  }
  if (confirm('Удалить этот сеанс?')) {
    PopupManager.load(`/admin.php?action=removeSeance&id=${id}`);
  }
}

function changeDate(delta) {
  console.log('changeDate called', delta);
  const dateSpan = document.getElementById('current-date');
  if (!dateSpan) return;

  const currentDate = dateSpan.textContent.split('.').reverse().join('-');
  const date = new Date(currentDate + 'T12:00:00');
  date.setDate(date.getDate() + delta);

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const newDate = `${year}-${month}-${day}`;
  const formattedDate = `${day}.${month}.${year}`;

  dateSpan.textContent = formattedDate;

  loadSessionsByDate(newDate);
}

function loadSessionsByDate(date) {
  console.log('loadSessionsByDate called', date);
  fetch(`/admin.php?action=getSessionsByDate&date=${date}`)
    .then(response => response.json())
    .then(data => {
      updateSeancesGrid(data);
    })
    .catch(error => console.error('Error:', error));
}

function updateSeancesGrid(data) {
  console.log('updateSeancesGrid called', data);
  const halls = data.halls || [];
  const sessions = data.sessions || [];

  const sessionsByHall = {};
  sessions.forEach(session => {
    if (!sessionsByHall[session.hall_id]) {
      sessionsByHall[session.hall_id] = [];
    }
    sessionsByHall[session.hall_id].push(session);
  });

  const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#D4A5A5', '#9B59B6', '#3498DB'];

  halls.forEach(hall => {
    const timeline = document.getElementById(`timeline-hall-${hall.id}`);
    if (!timeline) return;

    const hallSessions = sessionsByHall[hall.id] || [];

    hallSessions.sort((a, b) => new Date(a.start_time) - new Date(b.start_time));

    let html = '';
    hallSessions.forEach((session, index) => {
      const startTime = new Date(session.start_time);
      const startHour = startTime.getHours() + startTime.getMinutes() / 60;
      const duration = session.duration_min / 60;

      const left = (startHour - 9) * 60;
      const width = duration * 60;
      const color = colors[index % colors.length];

      html += `
        <div class="conf-step__seances-movie" 
             style="left: ${left}px; width: ${width}px; background-color: ${color};"
             onclick="showSeanceDetails(${session.id})"
             title="${escapeHtml(session.movie_title || 'Без названия')}">
          <p class="conf-step__seances-movie-title">${escapeHtml(session.movie_title || 'Без названия')}</p>
          <p class="conf-step__seances-movie-start">${startTime.getHours().toString().padStart(2, '0')}:${startTime.getMinutes().toString().padStart(2, '0')}</p>
          <button class="conf-step__seances-movie-delete" onclick="deleteSeance(${session.id}, event)">×</button>
        </div>
      `;
    });

    timeline.innerHTML = html;
  });
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closePopup();
  }
});

document.addEventListener('DOMContentLoaded', function () {
  console.log('Admin JS loaded successfully');

  const radios = document.querySelectorAll('input[name="chairs-hall"]');
  radios.forEach(radio => {
    radio.addEventListener('change', function () {
      cancelHallConfig();
    });
  });
});