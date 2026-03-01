const urlParams = new URLSearchParams(window.location.search);
const bookingCode = urlParams.get('booking_code');

if (!bookingCode) {
  window.location.href = '/client/index.php';
}

function loadTicket() {
  const container = document.getElementById('ticket-container');

  fetch(`/api.php?action=getBooking&code=${bookingCode}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        container.innerHTML = `<div class="error-message">${data.error}</div>`;
        return;
      }

      renderTicket(data);
    })
    .catch(error => {
      console.error('Error:', error);
      container.innerHTML = '<div class="error-message">Ошибка при загрузке билета</div>';
    });
}

function renderTicket(booking) {
  const container = document.getElementById('ticket-container');

  const seatTags = booking.seats.map(s =>
    `<span class="ticket__seat-tag">${s.row} ряд / ${s.col} место</span>`
  ).join('');

  const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${booking.code}`;

  let html = `
    <div class="ticket__header">
      <h2><span>Электронный</span> билет</h2>
    </div>
    
    <div class="ticket__content">
      <div class="ticket__qr">
        <img src="${qrCodeUrl}" alt="QR-код" id="qr-code">
      </div>
      
      <div class="ticket__info-grid">
        <div class="ticket__info-item">
          <div class="ticket__info-label">Код бронирования</div>
          <div class="ticket__info-value">${booking.code}</div>
        </div>
        <div class="ticket__info-item">
          <div class="ticket__info-label">Фильм</div>
          <div class="ticket__info-value">${escapeHtml(booking.movie_title)}</div>
        </div>
        <div class="ticket__info-item">
          <div class="ticket__info-label">Зал</div>
          <div class="ticket__info-value">${escapeHtml(booking.hall_name)}</div>
        </div>
        <div class="ticket__info-item">
          <div class="ticket__info-label">Дата и время</div>
          <div class="ticket__info-value">${booking.date} ${booking.start_time}</div>
        </div>
      </div>
      
      <div class="ticket__seats">
        <h3>Забронированные места:</h3>
        ${seatTags}
      </div>
      
      <div style="text-align: center;">
        <button class="ticket__print-button" onclick="printTicket()">
          🖨️ Распечатать билет
        </button>
        <button class="ticket__back-button" onclick="goBack()">
          ← На главную
        </button>
      </div>
      
      <div class="ticket__footer">
        <p>Покажите QR-код нашему контроллеру для подтверждения бронирования.</p>
        <p>Билет также отправлен на вашу электронную почту.</p>
        <p>Приятного просмотра!</p>
      </div>
    </div>
  `;

  container.innerHTML = html;
}

function printTicket() {
  const qrCode = document.getElementById('qr-code');
  if (qrCode) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <html>
        <head>
          <title>Электронный билет - ${bookingCode}</title>
          <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            img { max-width: 300px; margin: 30px; }
            h2 { color: #333; }
            .info { margin: 20px; color: #666; }
          </style>
        </head>
        <body>
          <h2>Электронный билет</h2>
          <img src="${qrCode.src}" alt="QR-код">
          <div class="info">Код бронирования: ${bookingCode}</div>
          <div class="info">Действителен только на один сеанс</div>
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
  }
}

function goBack() {
  window.location.href = '/client/index.php';
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('Ticket JS loaded');
  loadTicket();
});