class FurlleryGallery {
  constructor(id, el) {
    this.setProperties(id, el);
    this.addEvents();
  }

  setProperties(id, el) {
    this.id = id;
    this.current = -1;
    this.gallery = {};

    this.$container = jQuery( el );
    this.$overlay = jQuery( 'body .df-furllery-body-overlay' );
    this.$wrapper = jQuery( 'body .df-furllery-gallery-wrapper' );

    this.$header = this.$wrapper.find( '.df-furllery-header' );
    this.$headerContent = this.$header.find( '.df-furllery-header-content' );
    this.$preview = this.$wrapper.find( '.df-furllery-preview' );
    this.$image = this.$preview.find( '.df-furllery-image' );
    this.$panel = this.$wrapper.find( '.df-furllery-aside' );
    this.$footer = this.$wrapper.find( '.df-furllery-footer' );
    this.$author = this.$footer.find( '.df-furllery-meta-author' );
    this.$note = this.$footer.find( '.df-furllery-meta-note' );
  }

  addEvents() {
    jQuery( document ).on( 'keydown', event => {
      if ('Escape' === event.key) {
       this.closeGallery();
      }
    } );

    this.$overlay.on( 'click', '.df-close-button', () => this.closeGallery() );
    this.$overlay.on( 'click', '.df-toggle-panel-button', () => this.togglePanel() );

    this.$container.on( 'click', event => {
      event.preventDefault();

      this.$overlay.addClass( 'active' );
      this.$wrapper.addClass( 'active' );

      this.cleanGallery();
      this.loadGallery();
    } )

    this.$panel.on( 'click', '.df-furllery-panel-thumbnail', event => {
      event.preventDefault();
      this.displayImage( jQuery( event.currentTarget ).data('image') );
    } );
  }

  loadGallery() {
    jQuery.ajax({
      url: wp_core.ajaxurl,
      type: 'POST',
      data: {
        action: 'load_furllery',
        id: this.id,
      },
      success: this.buildGallery.bind( this ),
      error: function(xhr, status, error) {
        // Obsługa błędu
        console.error(error);
      }
    });
  }

  closeGallery() {
    this.$overlay.removeClass( 'active' );
    this.$wrapper.removeClass( 'active' );

    this.cleanGallery();
  }

  togglePanel() {
    this.$wrapper.toggleClass( 'no-panel' );
  }

  buildGallery(response) {
    if (!response?.success) {
      return this.closeGallery();
    }

    this.gallery = response?.data?.gallery ?? {};

    if ( 0 === Object.keys( this.gallery ).length ) {
      return this.closeGallery();
    }

    let index = 0;
    const $thumbnail = jQuery('<div class="df-furllery-panel-thumbnail" data-image=""><img src="" alt=""></div>');

    this.$headerContent.text( response?.data?.title ?? '' );

    for ( let image of this.gallery ) {
      const $newThumbnail = $thumbnail.clone();

      $newThumbnail.attr( 'data-image', index );
      $newThumbnail.find( 'img' ).attr( 'src', image?.thumbnail ).attr( 'alt', image?.meta?.note ?? '' );

      $newThumbnail.appendTo( this.$panel );

      index++;
    }

    this.displayImage(0)
  }

  displayImage(id) {
    this.$wrapper.addClass( 'no-loader' );
    this.$image.html( '' );
    this.$author.text( '' );
    this.$note.text( '' );

    const image = this.gallery[id];
    const $image = jQuery( '<img src="" alt="">' );

    $image.attr( 'src', image.url );
    $image.appendTo( this.$image );

    if ( Array.isArray( image?.meta?.author ) && 0 < image?.meta?.author.length ) {
      this.$author.text( image?.meta?.author[0] );
    }

    if ( Array.isArray( image?.meta?.note ) && 0 < image?.meta?.note.length ) {
      this.$note.text( image?.meta?.note[0] );
    }
  }

  cleanGallery() {
    this.$headerContent.text( '' );
    this.$panel.html( '' );
    this.$author.text( '' );
    this.$note.text( '' );
    this.$image.html( '' );
  }

}
