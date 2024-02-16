<?php

class FurlleryDB {
	public const string TABLE_GALLERIES = 'df_furllery_galleries';

	public function maybe_insert_gallery(): void {
		global $furllery_errors, $furllery_success_msg;

		$furllery_success_msg = '';

		if ( ! $this->is_request_valid() ) {
			return;
		}

		global $wpdb;

		$furllery_errors = [];

		$title       = sanitize_text_field( $_POST['title'] );
		$description = sanitize_textarea_field( $_POST['description'] );
		$content     = sanitize_text_field( $_POST['content'] );
		$active      = sanitize_text_field( $_POST['active'] );

		$content_json = json_decode( $content, true );

		if ( 0 >= mb_strlen( $title ) || 255 < mb_strlen( $title ) ) {
			$furllery_errors['title'] = __( 'Podanie tytułu jest obowiązkowe, musi on zawierać od 1 do 255 znaków.' );
		}

		if ( ! in_array( $active, [ '0', '1' ], true ) ) {
			$furllery_errors['active'] = __( 'Należy określić czy galeria ma być aktywna czy też nie.' );
		}

		if ( null === $content_json ) {
			$furllery_errors['content'] = __( 'Plugin nie może przetworzyć identyfikatorów dodanych zdjęć, spróbuj ponownie.' );
		}

		$wpdb->insert( $wpdb->prefix . static::TABLE_GALLERIES, [
			'title'       => $title,
			'description' => $description,
			'content'     => $content,
			'active'      => $active,
		], [ '%s', '%s', '%s', '%d' ] );

		$furllery_success_msg = sprintf( 'Galeria "%s" została utworzona.', $title );
	}

	protected function is_request_valid(): bool {
		return 'POST' === $_SERVER['REQUEST_METHOD'];
	}

}