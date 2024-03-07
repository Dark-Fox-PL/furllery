<?php

class Furllery {
	public function __construct() {
		return $this->initialize_plugin();
	}

	public function add_furllery_assets(): void {
		wp_enqueue_style( 'furllery-style', plugins_url( 'assets/furllery.css', dirname( __FILE__ ) ), [], '1.0.10' );

		wp_enqueue_script( 'lodash', '...' );
		wp_add_inline_script( 'lodash', 'window.lodash = _.noConflict();', 'after' );

		wp_enqueue_script( 'furllery-script', plugins_url( 'assets/furllery.js', dirname( __FILE__ ) ), [ 'jquery' ], '1.0.10', true );
		wp_enqueue_script( 'furllery-script-gallery', plugins_url( 'assets/furllery_gallery.js', dirname( __FILE__ ) ), [ 'jquery' ], '1.0.10', true );

		// Dodaj przekazanie zmiennej ajaxurl dla furllery_gallery.js
		wp_localize_script( 'furllery-script-gallery', 'wp_core', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
	}

	public function add_overlay(): void {
		echo '
		<div class="df-furllery-body-overlay">
			<div class="df-furllery-gallery-wrapper">
				<div class="df-furllery-header">
					<div class="df-close-button"></div>
					<div class="df-furllery-header-content"></div>
					<div class="df-toggle-panel-button"></div>
					
					<div class="df-furllery-header-description"></div>
				</div>
				<div class="df-furllery-preview">
					<div class="df-furllery-loader"></div>
					<div class="df-furllery-image"></div>
					<div class="df-furllery-navigate df-furllery-navigate-left"><div class="df-furllery-arrow-left"></div></div>
					<div class="df-furllery-navigate df-furllery-navigate-right"><div class="df-furllery-arrow-right"></div></div>
				</div>
				<div class="df-furllery-aside"></div>
				<div class="df-furllery-footer">
					<div class="df-furllery-meta-author"></div>
					<div class="df-furllery-meta-note"></div>
				</div>
			</div>
		</div>';
	}

	protected function initialize_plugin(): Furllery {
		return $this->maybe_run_admin()->run_plugin();
	}

	protected function run_plugin(): Furllery {
		return $this->add_fields_to_media_library()->create_shortcodes()->add_actions()->create_ajaxes();
	}

	protected function maybe_run_admin(): Furllery {
		if ( ! is_admin() ) {
			return $this;
		}

		$admin = new FurlleryAdmin;
		$admin->initialize();

		return $this;
	}

	protected function add_fields_to_media_library(): Furllery {
		$fields_add = fn( array $form_fields, ?WP_Post $post = null ) => $form_fields += [
			'furllery_author' => [
				'label' => __( 'Autor', 'df_furllery' ),
				'input' => 'text',
				'value' => get_post_meta( $post->ID, 'furllery_author', true ),
			],
			'furllery_note'   => [
				'label' => __( 'Notatka', 'df_furllery' ),
				'input' => 'textarea',
				'value' => get_post_meta( $post->ID, 'furllery_note', true ),
			],
		];

		$fields_save = function ( $post, $attachment ) {
			if ( isset( $attachment['furllery_author'] ) ) {
				update_post_meta( $post['ID'], 'furllery_author', sanitize_text_field( $attachment['furllery_author'] ) );
			} else {
				delete_post_meta( $post['ID'], 'furllery_author' );
			}

			if ( isset( $attachment['furllery_note'] ) ) {
				update_post_meta( $post['ID'], 'furllery_note', wp_kses_post( $attachment['furllery_note'] ) );
			} else {
				delete_post_meta( $post['ID'], 'furllery_note' );
			}

			return $post;
		};

		add_filter( 'attachment_fields_to_edit', $fields_add, 10, 2 );
		add_filter( 'attachment_fields_to_save', $fields_save, 10, 2 );

		return $this;
	}

	protected function add_actions(): Furllery {
		add_action( 'wp_enqueue_scripts', [ $this, 'add_furllery_assets' ] );
		add_action( 'wp_footer', [ $this, 'add_overlay' ] );

		return $this;
	}

	protected function create_shortcodes(): Furllery {
		$this->create_gallery_shortcode();
		$this->create_gallery_grid_shortcode();

		return $this;
	}

	protected function create_gallery_shortcode(): void {
		global $wpdb;

		add_shortcode( 'df_furllery', function ( $attr ) use ( $wpdb ): string {
			$default = [ 'id' => - 1 ];
			$attr    = shortcode_atts( $default, $attr );

			$id      = (int) esc_attr( $attr['id'] );
			$table   = $wpdb->prefix . FurlleryDB::TABLE_GALLERIES;
			$gallery = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

			if ( ! $gallery ) {
				return '';
			}

			$image = '';
			$title = $gallery['title'] ?? '';

			if ( 0 <= $gallery['thumbnail'] ) {
				$image = wp_get_attachment_image( $gallery['thumbnail'], [ 200, 200 ] );
			} else {
				$images = json_decode( $gallery['content'] ?? '[]', true );

				if ( is_array( $images ) && 0 < count( $images ) ) {
					$image = wp_get_attachment_image( $images[0], [ 200, 200 ] );
				}
			}

			return sprintf( '<div class="df-furllery" data-gallery="%d"><div class="df-furllery-overlay"><div class="df-furllery-title">%s</div></div>%s</div>', $id, $title, $image );
		} );
	}

	protected function create_gallery_grid_shortcode(): void {
		global $wpdb;

		add_shortcode( 'df_furllery_grid', function ( $attr ) use ( $wpdb ): string {
			$default = [ 'ids' => '', 'align' => 'center' ];
			$attr    = shortcode_atts( $default, $attr );
			$ids     = explode( ',', $attr['ids'] );

			if ( 0 === count( $ids ) ) {
				return '';
			}

			$wrapper  = '<div class="df-furllery-grid %s">%s</div>';
			$elements = [];
			$table    = $wpdb->prefix . FurlleryDB::TABLE_GALLERIES;

			foreach ( $ids as $id ) {
				$id = (int) esc_attr( $id );

				if ( 0 >= $id ) {
					continue;
				}

				$gallery = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

				if ( ! $gallery ) {
					continue;
				}

				$image = '';
				$title = $gallery['title'] ?? '';

				if ( 0 <= $gallery['thumbnail'] ) {
					$image = wp_get_attachment_image( $gallery['thumbnail'], [ 200, 200 ] );
				} else {
					$images = json_decode( $gallery['content'] ?? '[]', true );

					if ( is_array( $images ) && 0 < count( $images ) ) {
						$image = wp_get_attachment_image( $images[0], [ 200, 200 ] );
					}
				}

				$elements[] = sprintf( '<div class="df-furllery" data-gallery="%d"><div class="df-furllery-overlay"><div class="df-furllery-title">%s</div></div>%s</div>', $id, $title, $image );
			}

			if ( 0 === count( $elements ) ) {
				return '';
			}

			$alignment = esc_attr( $attr['align'] );
			$classes   = '';

			switch ($alignment) {
				case 'center':
					$classes = 'df-furllery-centered';
					break;
				case 'left':
					$classes = 'df-furllery-left';
					break;
				case 'right':
					$classes = 'df-furllery-right';
					break;
			}

			return vsprintf( $wrapper, [
				$classes,
				join( '', $elements ),
			] );
		} );
	}

	protected function create_ajaxes(): Furllery {
		$this->ajax__load_gallery();

		return $this;
	}

	protected function ajax__load_gallery() {
		global $wpdb;

		$load_gallery = function () use ( $wpdb ) {
			$gallery_id = (int) strip_tags( $_POST['id'] ?? - 1 );
			$table_name = $wpdb->prefix . FurlleryDB::TABLE_GALLERIES;

			$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE id = %d', $gallery_id ), );

			$is_active = '1' === ( $row->active ?? '0' );

			if ( ! $is_active ) {
				wp_send_json_error();
				wp_die();
			}

			$gallery = [];
			$media   = json_decode( $row->content ?? '[]', true ) ?? [];

			foreach ( $media as $media_id ) {
				$item = get_post( $media_id );

				if ( is_null( $item ) ) {
					continue;
				}

				$meta = get_post_meta( $media_id );

				$gallery[] = [
					'id'        => $item->ID,
					'url'       => wp_get_attachment_url( $media_id ),
					'thumbnail' => wp_get_attachment_thumb_url( $media_id ),
					'meta'      => [
						'author' => $meta['furllery_author'] ?? '',
						'note'   => $meta['furllery_note'] ?? '',
					],
				];
			}

			wp_send_json_success( [
				'title'       => $row->title ?? '',
				'description' => $row->description ?? '',
				'gallery'     => $gallery,
			] );
			wp_die();
		};

		add_action( 'wp_ajax_load_furllery', $load_gallery );
		add_action( 'wp_ajax_nopriv_load_furllery', $load_gallery );
	}

}