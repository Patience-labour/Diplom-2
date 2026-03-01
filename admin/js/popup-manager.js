const PopupManager = {
  currentPopup: null,

  open: function (html) {
    this.close();

    let container = document.getElementById('popup-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'popup-container';
      document.body.appendChild(container);
    }

    container.innerHTML = html;
    this.currentPopup = container.querySelector('.popup');

    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '15px';

    this.addEventListeners();

    return this.currentPopup;
  },

  close: function () {
    if (this.currentPopup) {
      this.currentPopup.remove();
      this.currentPopup = null;

      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }

    const container = document.getElementById('popup-container');
    if (container) {
      container.innerHTML = '';
    }
  },

  addEventListeners: function () {
    if (!this.currentPopup) return;

    this.currentPopup.addEventListener('click', (e) => {
      if (e.target === this.currentPopup) {
        this.close();
      }
    });

    const escapeHandler = (e) => {
      if (e.key === 'Escape') {
        this.close();
        document.removeEventListener('keydown', escapeHandler);
      }
    };
    document.addEventListener('keydown', escapeHandler);
  },

  load: function (url) {
    return fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(html => {
        this.open(html);
      })
      .catch(error => {
        console.error('Error loading popup:', error);
        alert('Ошибка при загрузке формы: ' + error.message);
      });
  }
};

function closePopup() {
  PopupManager.close();
}

function openPopup(url) {
  PopupManager.load(url);
}