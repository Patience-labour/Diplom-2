const urlParams = new URLSearchParams(window.location.search);
const bookingCode = urlParams.get('booking_code');

if (!bookingCode) {
  window.location.href = '/client/index.php';
}

function loadBooking() {
  const container = document.getElementById('booking-container');

  fetch(`/api.php?action=getBooking&code=${bookingCode}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        container.innerHTML = `<div class="error-message">${data.error}</div>`;
        return;
      }

      renderBooking(data);
    })
    .catch(error => {
      console.error('Error:', error);
      container.innerHTML = '<div class="error-message">Ошибка при загрузке данных</div>';
    });
}

function renderBooking(booking) {
  const container = document.getElementById('booking-container');

  const seatsText = booking.seats.map(s => `${s.row} ряд, ${s.col} место`).join('<br>');
  const seatsList = booking.seats.map(s =>
    `<div class="ticket__seat-item">
      <span>${s.row} ряд, ${s.col} место</span>
      <span>${s.price} ₽</span>
    </div>`
  ).join('');

  let html = `
    <div class="ticket__header">
      <h2>Подтверждение бронирования</h2>
    </div>
    
    <div class="ticket__content">
      <div class="ticket__info-row">
        <span class="ticket__label">Фильм:</span>
        <span class="ticket__value">${escapeHtml(booking.movie_title)}</span>
      </div>
      
      <div class="ticket__info-row">
        <span class="ticket__label">Зал:</span>
        <span class="ticket__value">${escapeHtml(booking.hall_name)}</span>
      </div>
      
      <div class="ticket__info-row">
        <span class="ticket__label">Дата:</span>
        <span class="ticket__value">${booking.date}</span>
      </div>
      
      <div class="ticket__info-row">
        <span class="ticket__label">Начало сеанса:</span>
        <span class="ticket__value">${booking.start_time}</span>
      </div>
      
      <div class="ticket__seats">
        <h3>Выбранные места:</h3>
        ${seatsList}
      </div>
      
      <div class="ticket__total">
        Итого: ${booking.total_price} ₽
      </div>
      
      <button class="ticket__button" onclick="confirmPayment()">
        Подтвердить бронирование
      </button>
      
      <p class="ticket__hint">
        После подтверждения вы получите электронные билеты с QR-кодами
      </p>
    </div>
  `;

  container.innerHTML = html;
}

function confirmPayment() {

  const button = document.querySelector('.ticket__button');
  button.textContent = 'Обработка...';
  button.disabled = true;

  setTimeout(() => {
    window.location.href = `/client/ticket.php?booking_code=${bookingCode}`;
  }, 1500);
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('Payment JS loaded');
  loadBooking();
});