class FurlleryAdmin {
  constructor() {
    this.setProperties();
    this.addEvents();
  }

  setProperties() {
    this.$galleryForm = jQuery('form.gallery-form');
    this.$selectedImages = this.$galleryForm.find('.images-wrapper');
    this.$singleImageTpl = this.$galleryForm.find('.image-wrapper');
    this.$galleryContent = this.$galleryForm.find('#gallery_content');
  }

  addEvents() {
    const $body = jQuery('body');

    // Handle Media Library.
    $body.on('click', '#furllery-select-images', event => {
      event.preventDefault();

      const media_frame = wp.media({
        title: 'Wybierz obraz',
        button: { text: 'Wybierz' },
        multiple: true,
      });

      media_frame.on( 'select', () => this.handleSelectedImages( media_frame ) );
      media_frame.open();
    });

    // Delete photo.
    $body.on('click', '.image-wrapper .delete-image', event => {
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
  }

  handleSelectedImages(media_frame) {
    const selection = media_frame.state().get('selection').toJSON();
    const gallery = JSON.parse(this.$galleryContent.val());

    for (const image of selection) {
      if (gallery.find((item) => item === image.id)) {
        continue;
      }

      let url = image?.sizes?.thumbnail?.url ?? '';

      if ('' === url) {
        url = image?.sizes?.full?.url ?? ''

        if ('' === url) {
          continue;
        }
      }

      gallery.push(image.id);

      const $holder = this.$singleImageTpl.clone();
      const $img = jQuery('<img alt="podgląd obrazka">').prop('src', url)

      $holder.attr('data-image-id', image.id);
      $holder.append($img);
      $holder.removeClass('hidden');

      this.$selectedImages.append($holder);
    }

    this.$galleryContent.val(JSON.stringify(gallery));
  }

}

jQuery(document).ready(() => new FurlleryAdmin());
