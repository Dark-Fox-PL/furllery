class Furllery {
  constructor() {
    this.setProperties();
    this.addEvents();
  }

  setProperties() {
    this.$overlay = jQuery( 'body .df-furllery-body-overlay' );
  }

  addEvents() {
    jQuery('body').on( 'click', '.df-furllery', event => {
      event.preventDefault();

      new FurlleryGallery(event.currentTarget);
    } );
  }

}

jQuery(document).ready(() => new Furllery());
