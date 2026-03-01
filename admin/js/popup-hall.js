function closePopup() {
  const popup = document.querySelector('.popup');
  if (popup) {
    popup.remove();
    document.body.style.overflow = '';
  }
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('Popup hall JS loaded');

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closePopup();
    }
  });

  const popup = document.querySelector('.popup');
  if (popup) {
    popup.addEventListener('click', function (e) {
      if (e.target === this) {
        closePopup();
      }
    });
  }
});