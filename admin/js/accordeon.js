document.addEventListener('DOMContentLoaded', function () {
  var headers = document.querySelectorAll('.conf-step__header');

  for (var i = 0; i < headers.length; i++) {
    headers[i].addEventListener('click', function () {
      this.classList.toggle('conf-step__header_closed');
      this.classList.toggle('conf-step__header_opened');

      var content = this.nextElementSibling;
      if (content && content.classList.contains('conf-step__wrapper')) {
        if (content.style.display === 'none') {
          content.style.display = 'block';
        } else {
          content.style.display = 'none';
        }
      }
    });
  }
});