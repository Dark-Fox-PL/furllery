<div class="wrap">
  <h1 class="wp-heading-inline">
	  <?php
	  echo isset( $gallery_data['title'] ) ? esc_html__( 'Furllery - Edytuj galerię' ) : esc_html__( 'Furllery - Nowa galeria' );
	  ?>
  </h1>
  <hr class="wp-header-end">

	<?php if ( 0 < count( $furllery_errors ) ): ?>
      <div class="notice notice-error is-dismissible">
        <p class="error-holder">
          <strong>Coś poszło nie tak:</strong>
        <ul>
			<?php foreach ( $furllery_errors as $key => $error ): ?>
              <li><?php echo $error; ?></li>
			<?php endforeach; ?>
        </ul>
        </p>
      </div>
	<?php endif; ?>

	<?php if ( isset( $_GET['success'] ) && '1' === $_GET['success'] ): ?>
    <div class="notice notice-success is-dismissible">
      <?php if ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] ): ?>
      <p>Galeria "<?php echo esc_attr( $gallery_data['title'] ); ?>" została zaktualizowana.</p>
      <?php elseif ( isset( $_GET['action'] ) && 'create' === $_GET['action'] ): ?>
        <p>Galeria "<?php echo esc_attr( $gallery_data['title'] ); ?>" została utworzona.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

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
          <td>
            <input
              name="title"
              type="text"
              id="gallery_title"
              value="<?php echo esc_attr( $gallery_data['title'] ?? '' ) ?>"
              aria-required="true"
              autocapitalize="none"
              autocorrect="off"
              autocomplete="off"
              maxlength="255"
              class="regular-text <?php echo isset( $furllery_errors['title'] ) ? 'has-error' : ''; ?>"
            >
          </td>
        </tr>

        <tr class="form-field">
          <th scope="row"><label for="gallery_description"><?php echo esc_html__( 'Opis', 'df_furllery' ) ?></label>
          </th>
          <td>
          <?php

          $editor_args = [
            'textarea_name' => 'description',
            'textarea_rows' => 5,
            'media_buttons' => false,
            'tinymce'       => false,
          ];
          wp_editor( $gallery_data['description'] ?? '', 'gallery_description', $editor_args );

          ?>
          </td>
        </tr>

        <tr class="form-field form-required">
          <th scope="row"><label
              for="gallery_active"><?php echo esc_html__( 'Galeria aktywna', 'df_furllery' ) ?></label></th>
          <td>
            <select id="gallery_active" name="active">
              <option value="1" <?php selected( $gallery_data['active'] ?? '', 1 ); ?>>Tak</option>
              <option value="0" <?php selected( $gallery_data['active'] ?? '', 0 ); ?>>Nie</option>
            </select>
          </td>
        </tr>

        <tr class="form-field form-required">
          <th scope="row"><label for="gallery_content"><?php echo esc_html__( 'Zdjęcia', 'df_furllery' ) ?></label></th>
          <td>
            <input name="content" type="hidden" id="gallery_content"
                   value="<?php echo esc_attr( $gallery_data['content'] ?? '[]' ) ?>" aria-required="true"
                   autocapitalize="none" autocorrect="off" autocomplete="off">
            <button id="furllery-select-images" class="page-title-action"
                    type="button"><?php echo esc_html__( 'Wybierz zdjęcia', 'df_furllery' ); ?></button>

            <div class="images-wrapper">
				<?php foreach ( $images_urls as $image_id => $image_url ): ?>
                  <div class="image-wrapper" data-image-id="<?php echo esc_attr( $image_id ); ?>">
                    <div class="dashicons-before dashicons-trash delete-image"></div>
                    <img src="<?php echo $image_url; ?>" alt="Element galerii" width="150" height="150">
                  </div>
				<?php endforeach; ?>
            </div>
          </td>
        </tr>
        </tbody>
      </table>

      <p class="submit"><input type="submit" name="add_gallery" id="add-gallery-sub" class="button button-primary"
                               value="<?php echo isset( $gallery_data['title'] ) ? esc_html__( 'Aktualizuj galerię', 'df_furllery' ) : esc_html__( 'Utwórz galerię', 'df_furllery' ) ?>">
      </p>
    </form>
  </div>

