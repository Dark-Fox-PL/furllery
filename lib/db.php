<?php

class FurlleryDB {
	public const string TABLE_GALLERIES = 'df_furllery_galleries';

	public function maybe_update_gallery( int $edit_id ): void {
		global $furllery_errors;

		$furllery_errors = [];

		if ( ! $this->is_request_valid() ) {
			return;
		}

		global $wpdb;
		[ $title, $description, $content, $active ] = $this->get_validated_data();

		if ( 0 < count( $furllery_errors ) ) {
			return;
		}

		$wpdb->update( $wpdb->prefix . static::TABLE_GALLERIES, [
			'title'       => $title,
			'description' => $description,
			'content'     => $content,
			'active'      => $active,
		], [ 'id' => $edit_id ], [ '%s', '%s', '%s', '%d' ], [ '%d' ] );

		$query_args   = [
			'edit_id' => $edit_id,
			'action'  => 'edit',
			'success' => true,
		];
		$redirect_url = add_query_arg( $query_args, admin_url( 'admin.php?page=furllery__upsert_gallery' ) );

		if ( wp_redirect( $redirect_url ) ) {
			exit;
		}
	}

	public function maybe_insert_gallery(): void {
		global $furllery_errors;

		$furllery_errors = [];

		if ( ! $this->is_request_valid() ) {
			return;
		}

		global $wpdb;
		[ $title, $description, $content, $active ] = $this->get_validated_data();

		if ( 0 < count( $furllery_errors ) ) {
			return;
		}

		$result = $wpdb->insert( $wpdb->prefix . static::TABLE_GALLERIES, [
			'title'       => $title,
			'description' => $description,
			'content'     => $content,
			'active'      => $active,
		], [ '%s', '%s', '%s', '%d' ] );

		if ( $result ) {
			$query_args   = [
				'edit_id' => $wpdb->insert_id,
				'action'  => 'create',
				'success' => true,
			];
			$redirect_url = add_query_arg( $query_args, admin_url( 'admin.php?page=furllery__upsert_gallery' ) );

			if ( wp_redirect( $redirect_url ) ) {
				exit;
			}
		}
	}

	public function delete_gallery( int $gallery_id ): bool {
		global $wpdb;

		$table_name = $wpdb->prefix . static::TABLE_GALLERIES;
		return (bool) $wpdb->delete( $table_name, [ 'id' => $gallery_id ], [ '%d' ] );
	}

	protected function get_validated_data(): array {
		global $furllery_errors;

		$title       = sanitize_text_field( $_POST['title'] );
		$description = wp_kses_post( $_POST['description'] );
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

		return [ $title, $description, $content, $active ];
	}

	protected function is_request_valid(): bool {
		return 'POST' === $_SERVER['REQUEST_METHOD'];
	}

}