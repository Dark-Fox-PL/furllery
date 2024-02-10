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
    // Handle Media Library
    jQuery('body').on('click', '#furllery-select-images', event => {
      event.preventDefault();

      const media_frame = wp.media({
        title: 'Wybierz obraz',
        button: { text: 'Wybierz' },
        multiple: true,
        library: { type: 'image' }
      });

      media_frame.on( 'select', () => this.handleSelectedImages( media_frame ) );

      media_frame.open();
    });
  }

  handleSelectedImages(media_frame) {
    const selection = media_frame.state().get('selection').toJSON();
    const gallery = JSON.parse(this.$galleryContent.val());

    for (const image of selection) {
      if (gallery.find((item) => item === image.id)) {
        continue;
      }

      const url = image?.sizes?.thumbnail?.url ?? '';

      if ('' === url) {
        continue;
      }

      gallery.push(image.id);

      const $holder = this.$singleImageTpl.clone();
      const $img = jQuery('<img alt="podgląd obrazka">').prop('src', url)

      $holder.attr('data-image-id', image.id);
      $holder.append($img);

      this.$selectedImages.append($holder);
    }

    this.$galleryContent.val(JSON.stringify(gallery));
  }

}

jQuery(document).ready(() => new FurlleryAdmin());
