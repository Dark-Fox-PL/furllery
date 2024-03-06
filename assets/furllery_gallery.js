class FurlleryGallery {
  constructor(el) {
    this.setProperties(el);
    this.addEvents();

    this.$overlay.addClass( 'active' );
    this.$wrapper.addClass( 'active' );

    this.cleanGallery();
    this.loadGallery();
  }

  setProperties(el) {
    this.id = el.dataset.gallery || -1;
    this.current = -1;
    this.lastImage = -1;
    this.gallery = {};

    this.$overlay = jQuery( 'body .df-furllery-body-overlay' );
    this.$wrapper = jQuery( 'body .df-furllery-gallery-wrapper' );

    this.$header = this.$wrapper.find( '.df-furllery-header' );
    this.$headerContent = this.$header.find( '.df-furllery-header-content' );
    this.$headerDescription = this.$header.find( '.df-furllery-header-description' );
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

      if (event.keyCode === 37) {
        this.navigate();
      } else if (event.keyCode === 39) {
        this.navigate(false);
      }
    } );

    this.$overlay.on( 'click', '.df-close-button', () => this.closeGallery() );
    this.$overlay.on( 'click', '.df-toggle-panel-button', () => this.togglePanel() );
    this.$overlay.on( 'click', '.df-furllery-navigate-left', () => this.navigate() );
    this.$overlay.on( 'click', '.df-furllery-navigate-right', () => this.navigate(false) );



    this.$panel.on( 'click', '.df-furllery-panel-thumbnail', event => {
      event.preventDefault();
      this.displayImage( jQuery( event.currentTarget ).data('image') );
    } );
  }

  navigate(isLeft = true) {
    const check = isLeft ? this.current - 1 : this.current + 1;

    if ((isLeft && check < 0) || (!isLeft && check > this.lastImage)) {
      this.current = isLeft ? this.lastImage : 0;
    } else {
      this.current = check;
    }

    this.displayImage(this.current);
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

    this.$wrapper.toggleClass( 'no-navigation', 1 >= Object.keys( this.gallery ).length )

    let index = 0;
    const $thumbnail = jQuery('<div class="df-furllery-panel-thumbnail" data-image=""><img src="" alt=""></div>');

    this.$headerContent.text( response?.data?.title ?? '' );
    this.$headerDescription.text( response?.data?.description ?? '' );

    for ( let image of this.gallery ) {
      const $newThumbnail = $thumbnail.clone();

      this.lastImage = index;

      $newThumbnail.attr( 'data-image', index );
      $newThumbnail.find( 'img' ).attr( 'src', image?.thumbnail ).attr( 'alt', image?.meta?.note ?? '' );

      $newThumbnail.appendTo( this.$panel );

      index++;
    }

    this.displayImage(0)
  }

  displayImage(id) {
    this.current = id;

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
      this.$note.html( image?.meta?.note[0] );
    }

    this.setActiveThumbnail();
  }

  cleanGallery() {
    this.$headerContent.text( '' );
    this.$headerDescription.text( '' );
    this.$panel.html( '' );
    this.$author.text( '' );
    this.$note.text( '' );
    this.$image.html( '' );
  }

  setActiveThumbnail() {
    this.$panel.find( '.df-furllery-panel-thumbnail' ).removeClass( 'active' );
    this.$panel.find( `.df-furllery-panel-thumbnail[data-image=${this.current}]` ).addClass( 'active' )
  }

}
