class Furllery {
  constructor() {
    this.setProperties();
    this.addEvents();

    this.maybeCreateGalleries();
  }

  setProperties() {
    this.$galleries = jQuery( 'body .df-furllery' );
    this.$overlay = jQuery( 'body .df-furllery-body-overlay' );
    this.$wrapper = jQuery( 'body .df-furllery-gallery-wrapper' );
  }

  addEvents() {

  }

  maybeCreateGalleries() {
    if (0 === this.$galleries.length) {
      return;
    }

    for ( let gallery of this.$galleries ) {
      const id = gallery.dataset.gallery ?? -1;

      if (-1 === id) {
        continue;
      }

      new FurlleryGallery( id, gallery );
    }
  }

}

jQuery(document).ready(() => new Furllery());
