class FurlleryGallery {
  constructor(id, el) {
    this.setProperties(id, el);
    this.addEvents();
  }

  setProperties(id, el) {
    this.id = id;

    this.$container = jQuery( el );
    this.$overlay = jQuery( 'body .df-furllery-body-overlay' );
    this.$wrapper = jQuery( 'body .df-furllery-gallery-wrapper' );
  }

  addEvents() {
    jQuery( document ).on( 'keydown', event => {
      if ('Escape' === event.key) {
       this.closeGallery();
      }
    } );
    this.$overlay.on( 'click', '.df-close-button', () => this.closeGallery() );

    this.$container.on( 'click', event => {
      event.preventDefault();

      this.$overlay.addClass( 'active' );
      this.$wrapper.addClass( 'active' );
      this.loadGallery();
    } )
  }

  loadGallery() {
    jQuery.ajax({
      url: wp_core.ajaxurl,
      type: 'POST',
      data: {
        action: 'load_furllery',
        id: this.id,
      },
      success: function(response) {
        // Obsługa udanej odpowiedzi
        console.log(response);
      },
      error: function(xhr, status, error) {
        // Obsługa błędu
        console.error(error);
      }
    });
  }

  closeGallery() {
    this.$overlay.removeClass( 'active' );
    this.$wrapper.removeClass( 'active' );
  }

}
