class FurlleryAdmin {
  constructor() {
    this.addEvents();
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

      media_frame.on( 'select', () => {
        const selection = media_frame.state().get('selection').toJSON();

        console.log( selection );
      } );

      media_frame.open();
    });
  }

}

jQuery(document).ready(() => new FurlleryAdmin());
