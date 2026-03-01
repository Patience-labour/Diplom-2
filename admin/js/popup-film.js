function closePopup() {
  const popup = document.querySelector('.popup');
  if (popup) {
    popup.remove();
    document.body.style.overflow = '';
  }
}

function previewPoster(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const oldPreview = document.getElementById('poster-preview');
      if (oldPreview) {
        oldPreview.remove();
      }

      const img = document.createElement('img');
      img.id = 'poster-preview';
      img.src = e.target.result;
      img.alt = 'Превью постера';
      img.style.maxWidth = '150px';
      img.style.maxHeight = '150px';
      img.style.marginTop = '10px';
      img.style.border = '1px solid #ddd';
      img.style.borderRadius = '4px';
      img.style.padding = '3px';

      const container = document.getElementById('poster-preview-container');
      if (container) {
        container.innerHTML = '';
        container.appendChild(img);
      } else {
        input.parentNode.appendChild(img);
      }
    }
    reader.readAsDataURL(input.files[0]);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  console.log('Popup film JS loaded');

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