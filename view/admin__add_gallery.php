<div class="wrap">
  <h1 class="wp-heading-inline"><?php echo esc_html__( 'Furllery - Nowa galeria' ); ?></h1>
  <hr class="wp-header-end">

  <form method="post" name="add-gallery" id="add-gallery" class="validate gallery-form" novalidate="novalidate">
    <div class="image-wrapper hidden" data-image-id="">
      <div class="dashicons-before dashicons-trash delete-image"></div>
    </div>
	  <?php wp_nonce_field( 'add-gallery', '_wpnonce_add-gallery' ); ?>

    <input name="action" type="hidden" value="add_gallery">

    <table class="form-table" role="presentation">
      <tbody>
        <tr class="form-field form-required">
          <th scope="row"><label for="gallery_title"><?php echo esc_html__( 'Nazwa', 'df_furllery' ) ?></label></th>
          <td><input name="title" type="text" id="gallery_title" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" maxlength="255" class="regular-text"></td>
        </tr>

        <tr class="form-field">
          <th scope="row"><label for="gallery_description"><?php echo esc_html__( 'Opis', 'df_furllery' ) ?></label></th>
          <td>
	          <?php

	          $editor_args = [
		          'textarea_name' => 'content',
		          'textarea_rows' => 5,
		          'media_buttons' => false,
		          'tinymce'       => false,
	          ];
	          wp_editor('', 'gallery_description', $editor_args);

	          ?>
          </td>
        </tr>

        <tr class="form-field form-required">
          <th scope="row"><label for="gallery_active"><?php echo esc_html__( 'Galeria aktywna', 'df_furllery' ) ?></label></th>
          <td>
            <select id="gallery_active" name="active">
              <option value="1" selected>Tak</option>
              <option value="0">Nie</option>
            </select>
          </td>
        </tr>

        <tr class="form-field form-required">
          <th scope="row"><label for="gallery_content"><?php echo esc_html__( 'Zdjęcia', 'df_furllery' ) ?></label></th>
          <td>
            <input name="content" type="hidden" id="gallery_content" value="[]" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off">
            <button id="furllery-select-images" class="page-title-action" type="button"><?php echo esc_html__( 'Wybierz zdjęcia', 'df_furllery' ); ?></button>
            <div class="images-wrapper"></div>
          </td>
        </tr>
      </tbody>
    </table>

    <p class="submit"><input type="submit" name="add_gallery" id="add-gallery-sub" class="button button-primary" value="<?php echo esc_html__( 'Utwórz galerię', 'df_furllery' ) ?>"></p>
  </form>
</div>

