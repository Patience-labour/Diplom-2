function closePopup() {
  const popup = document.querySelector('.popup');
  if (popup) {
    popup.remove();
    document.body.style.overflow = '';
  }
}

function updateMovieInfo(select) {
  const option = select.options[select.selectedIndex];
  const infoDiv = document.getElementById('movie-info');
  
  if (option.value) {
    const duration = option.dataset.duration;
    const endTime = calculateEndTime(document.getElementById('time').value, duration);
    
    infoDiv.innerHTML = `
      <p><strong>${option.text}</strong></p>
      <p>Продолжительность: ${duration} минут</p>
      <p>Окончание сеанса: ~${endTime}</p>
    `;
  } else {
    infoDiv.innerHTML = '<p>Выберите фильм для просмотра информации</p>';
  }
}

function calculateEndTime(startTime, duration) {
  const [hours, minutes] = startTime.split(':').map(Number);
  const totalMinutes = hours * 60 + minutes + parseInt(duration);
  const endHours = Math.floor(totalMinutes / 60) % 24;
  const endMinutes = totalMinutes % 60;
  return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
}

document.addEventListener('DOMContentLoaded', function() {
  console.log('Popup seance JS loaded');
  
  const timeInput = document.getElementById('time');
  if (timeInput) {
    timeInput.addEventListener('change', function() {
      const movieSelect = document.getElementById('movie_id');
      if (movieSelect && movieSelect.value) {
        updateMovieInfo(movieSelect);
      }
    });
  }
  
  const movieSelect = document.getElementById('movie_id');
  if (movieSelect) {
    movieSelect.addEventListener('change', function() {
      updateMovieInfo(this);
    });
  }
  
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closePopup();
    }
  });
  
  const popup = document.querySelector('.popup');
  if (popup) {
    popup.addEventListener('click', function(e) {
      if (e.target === this) {
        closePopup();
      }
    });
  }
});