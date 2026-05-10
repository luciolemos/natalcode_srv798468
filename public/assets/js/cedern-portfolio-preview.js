(function () {
  function clearStage(stage) {
    while (stage.firstChild) {
      stage.removeChild(stage.firstChild);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    var dialog = document.querySelector('[data-portfolio-preview-dialog]');
    var stage = document.querySelector('[data-portfolio-preview-stage]');
    var caption = document.querySelector('[data-portfolio-preview-caption]');
    var closeButton = document.querySelector('[data-portfolio-preview-close]');
    var triggers = document.querySelectorAll('[data-portfolio-preview-trigger]');

    if (!dialog || !stage || !caption || !closeButton || !triggers.length) {
      return;
    }

    function closeDialog() {
      if (typeof dialog.close === 'function' && dialog.open) {
        dialog.close();
      } else {
        dialog.removeAttribute('open');
      }

      clearStage(stage);
      caption.hidden = true;
      caption.textContent = '';
    }

    function openDialog(trigger) {
      var imageSrc = trigger.getAttribute('data-preview-image') || '';
      var imageTitle = trigger.getAttribute('data-preview-title') || '';

      if (!imageSrc) {
        return;
      }

      clearStage(stage);

      var image = document.createElement('img');
      image.className = 'nc-portfolio-preview-image';
      image.src = imageSrc;
      image.alt = imageTitle;
      image.decoding = 'async';
      stage.appendChild(image);

      var captionText = imageTitle.trim();
      if (captionText) {
        caption.textContent = captionText;
        caption.hidden = false;
      } else {
        caption.hidden = true;
        caption.textContent = '';
      }

      if (typeof dialog.showModal === 'function') {
        dialog.showModal();
      } else {
        dialog.setAttribute('open', 'open');
      }
    }

    triggers.forEach(function (trigger) {
      trigger.addEventListener('click', function (event) {
        event.preventDefault();
        openDialog(trigger);
      });
    });

    closeButton.addEventListener('click', closeDialog);

    dialog.addEventListener('click', function (event) {
      if (event.target === dialog) {
        closeDialog();
      }
    });

    dialog.addEventListener('close', function () {
      clearStage(stage);
      caption.hidden = true;
      caption.textContent = '';
    });
  });
})();
