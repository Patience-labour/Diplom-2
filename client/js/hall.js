const urlParams = new URLSearchParams(window.location.search);
const sessionId = urlParams.get('session_id');

if (!sessionId) {
  window.location.href = '/client/index.php';
}

let sessionData = null;
let selectedSeats = [];

function loadSession() {
  const container = document.getElementById('hall-container');

  fetch(`/api.php?action=getSession&id=${sessionId}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        container.innerHTML = `<div class="error-message">${data.error}</div>`;
        return;
      }

      sessionData = data;
      renderHall();
    })
    .catch(error => {
      console.error('Error:', error);
      container.innerHTML = '<div class="error-message">Ошибка при загрузке данных</div>';
    });
}

function renderHall() {
  const container = document.getElementById('hall-container');

  let html = `
    <div class="buying__info">
      <div class="buying__info-description">
        <h2 class="buying__info-title">${escapeHtml(sessionData.movie_title)}</h2>
        <p class="buying__info-start">Начало сеанса: ${sessionData.start_time}</p>
        <p class="buying__info-hall">${escapeHtml(sessionData.hall_name)}</p>          
      </div>
      <div class="buying__info-hint">
        <p>Тапните дважды,<br>чтобы увеличить</p>
      </div>
    </div>
    
    <div class="buying-scheme">
      <div class="buying-scheme__wrapper">
        ${renderSeats()}
      </div>
      
      <div class="buying-scheme__legend">
        <div class="col">
          <p class="buying-scheme__legend-price">
            <span class="buying-scheme__chair buying-scheme__chair_standart"></span> 
            Свободно (<span class="buying-scheme__legend-value">${sessionData.price_standart}</span>₽)
          </p>
          <p class="buying-scheme__legend-price">
            <span class="buying-scheme__chair buying-scheme__chair_vip"></span> 
            Свободно VIP (<span class="buying-scheme__legend-value">${sessionData.price_vip}</span>₽)
          </p>            
        </div>
        <div class="col">
          <p class="buying-scheme__legend-price">
            <span class="buying-scheme__chair buying-scheme__chair_taken"></span> Занято
          </p>
          <p class="buying-scheme__legend-price">
            <span class="buying-scheme__chair buying-scheme__chair_selected"></span> Выбрано
          </p>                    
        </div>
      </div>
    </div>
    
    <button class="acceptin-button" onclick="bookTickets()" ${selectedSeats.length === 0 ? 'disabled' : ''}>
      Забронировать (${selectedSeats.length} мест)
    </button>
  `;

  container.innerHTML = html;
}

function renderSeats() {
  let html = '';
  const seats = sessionData.seats || [];
  const bookedSeats = sessionData.booked_seats || [];

  const seatsByRow = {};
  seats.forEach(seat => {
    if (!seatsByRow[seat.row]) {
      seatsByRow[seat.row] = [];
    }
    seatsByRow[seat.row].push(seat);
  });

  const rows = Object.keys(seatsByRow).sort((a, b) => a - b);

  rows.forEach(row => {
    html += '<div class="buying-scheme__row">';

    const rowSeats = seatsByRow[row].sort((a, b) => a.col - b.col);

    rowSeats.forEach(seat => {
      const isBooked = bookedSeats.includes(seat.id);
      const isSelected = selectedSeats.includes(seat.id);

      let seatClass = 'buying-scheme__chair';
      if (!seat.is_active || seat.type === 'disabled') {
        seatClass += ' buying-scheme__chair_disabled';
      } else if (isBooked) {
        seatClass += ' buying-scheme__chair_taken';
      } else if (isSelected) {
        seatClass += ' buying-scheme__chair_selected';
      } else if (seat.type === 'vip') {
        seatClass += ' buying-scheme__chair_vip';
      } else {
        seatClass += ' buying-scheme__chair_standart';
      }

      html += `<span class="${seatClass}" 
                     data-seat-id="${seat.id}"
                     data-row="${seat.row}"
                     data-col="${seat.col}"
                     data-type="${seat.type}"
                     data-price="${seat.type === 'vip' ? sessionData.price_vip : sessionData.price_standart}"
                     onclick="toggleSeat(this)"></span>`;
    });

    html += '</div>';
  });

  return html;
}

function toggleSeat(element) {
  if (element.classList.contains('buying-scheme__chair_disabled') ||
    element.classList.contains('buying-scheme__chair_taken')) {
    return;
  }

  const seatId = parseInt(element.dataset.seatId);

  if (element.classList.contains('buying-scheme__chair_selected')) {
    element.classList.remove('buying-scheme__chair_selected');
    if (element.dataset.type === 'vip') {
      element.classList.add('buying-scheme__chair_vip');
    } else {
      element.classList.add('buying-scheme__chair_standart');
    }
    selectedSeats = selectedSeats.filter(id => id !== seatId);
  } else {
    element.classList.remove('buying-scheme__chair_vip', 'buying-scheme__chair_standart');
    element.classList.add('buying-scheme__chair_selected');
    selectedSeats.push(seatId);
  }

  updateBookButton();
}

function updateBookButton() {
  const button = document.querySelector('.acceptin-button');
  if (button) {
    button.textContent = `Забронировать (${selectedSeats.length} мест)`;
    button.disabled = selectedSeats.length === 0;
  }
}

function bookTickets() {
  if (selectedSeats.length === 0) return;

  fetch('/api.php?action=bookTickets', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      session_id: sessionId,
      seats: selectedSeats
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.href = `/client/payment.php?booking_code=${data.booking_code}`;
      } else {
        alert('Ошибка при бронировании: ' + (data.error || 'неизвестная ошибка'));
        loadSession();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Ошибка при бронировании');
    });
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('Hall JS loaded');
  loadSession();
});