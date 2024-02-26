class FurlleryAdmin {
  constructor() {
    this.setProperties();
    this.addEvents();
    this.addToastContainer();
    this.sortImages();
  }

  setProperties() {
    this.$galleryForm = jQuery('form.gallery-form');
    this.$selectedImages = this.$galleryForm.find('.images-wrapper');
    this.$singleImageTpl = this.$galleryForm.find('.image-wrapper.hidden');
    this.$galleryContent = this.$galleryForm.find('#gallery_content');
    this.$thumbnailWrapper = this.$galleryForm.find('.thumbnail-wrapper');
    this.$thumbnailContent = this.$galleryForm.find('#gallery_thumbnail');
  }

  addEvents() {
    const $body = jQuery('body');

    // Handle Media Library.
    $body.on('click', '#furllery-select-images', event => {
      event.preventDefault();

      const media_frame = wp.media({
        title: 'Wybierz obraz',
        button: {text: 'Wybierz'},
        multiple: true,
      });

      media_frame.on('select', () => this.handleSelectedImages(this.$selectedImages, this.$galleryContent, media_frame));
      media_frame.open();
    });

    $body.on('click', '#furllery-select-thumbnail', event => {
      event.preventDefault();

      const media_frame = wp.media({
        title: 'Wybierz okładkę',
        button: {text: 'Wybierz'},
        multiple: false,
      });

      media_frame.on('select', () => this.handleSelectedImages(this.$thumbnailWrapper, this.$thumbnailContent, media_frame));
      media_frame.open();
    });

    // Delete photo.
    $body.on('click', '.images-wrapper .image-wrapper .delete-image', event => {
      event.preventDefault();

      const $target = jQuery(event.target).parent();
      const imageId = $target.data('image-id') ?? -1;

      if (-1 === imageId) {
        alert('Nope...');
        return;
      }

      const result = confirm('Czy chcesz usunąć ten obrazek?');

      if (!result) {
        return;
      }

      const gallery = JSON.parse(this.$galleryContent.val());

      gallery.filter((value, index, source) => {
        if (value === imageId) {
          source.splice(index, 1);
          return true;
        }

        return false;
      });

      $target.remove();
      this.$galleryContent.val(JSON.stringify(gallery));
    });

    // Delete thumbnail
    $body.on('click', '.thumbnail-wrapper .image-wrapper .delete-image', event => {
      event.preventDefault();

      const $target = jQuery(event.target).parent();
      const imageId = $target.data('image-id') ?? -1;

      if (-1 === imageId) {
        alert('Nope...');
        return;
      }

      const result = confirm('Czy chcesz usunąć okładkę galerii?');

      if (!result) {
        return;
      }

      this.$galleryContent.val(-1);
    });
  }

  handleSelectedImages($wrapper, $input, media_frame) {
    const selection = media_frame.state().get('selection').toJSON();
    const gallery = JSON.parse($input.val());
    const isGalleryObject = 'object' === typeof gallery;

    let imageId = -1;

    for (const image of selection) {
      if (isGalleryObject && gallery.find((item) => item === image.id)) {
        continue;
      }

      let url = image?.sizes?.thumbnail?.url ?? '';

      if ('' === url) {
        url = image?.sizes?.full?.url ?? ''

        if ('' === url) {
          continue;
        }
      }

      if (isGalleryObject) {
        gallery.push(image.id);
      } else {
        imageId = image.id;
      }

      const $holder = this.$singleImageTpl.clone();
      const $img = jQuery('<img alt="podgląd obrazka">').prop('src', url)

      $holder.attr('data-image-id', image.id);
      $holder.append($img);
      $holder.removeClass('hidden');

      $holder.appendTo($wrapper);
    }

    $input.val(isGalleryObject ? JSON.stringify(gallery) : imageId);
  }

  addToastContainer() {
    const $body = jQuery('body');
    const $container = $body.find('furllery-toast-container');

    if (0 !== $container.length) {
      return;
    }

    $body.append('<div class="furllery-toast-container">')
  }

  sortImages() {
    this.$selectedImages.sortable({
      placeholder: 'df-sortable-placeholder',
      tolerance: 'pointer',

      start: () => { this.$selectedImages.find( '.delete-image' ).addClass( 'hidden' ) },
      stop: () => { this.$selectedImages.find( '.delete-image' ).removeClass( 'hidden' ) },

      update: () => {
        const gallery = [];

        this.$selectedImages.find('.image-wrapper').each((index, image) => {
          gallery.push(parseInt(image.dataset.imageId));
        })

        console.log( JSON.stringify(gallery) );
        this.$galleryContent.val(JSON.stringify(gallery));
      }
    });

    this.$selectedImages.disableSelection();
  }

}

jQuery(document).ready(() => new FurlleryAdmin());

function furllery__copyToClipboard(element, text) {
  const textarea = document.createElement('textarea');

  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);

  furllery__showToast('Skopiowano tekst: ' + text);
}

function furllery__showToast(message) {
  const $toast = jQuery('<div class="furllery-toast">');

  $toast.text(message);
  jQuery('body .furllery-toast-container').append($toast);

  setTimeout(() => {
    setTimeout(() => {
      $toast.fadeOut(400, () => $toast.remove());
    }, 1800);
  }, 100);
}