<?php

class Furllery {
	public function __construct() {
		return $this->initialize_plugin();
	}

	public function add_furllery_js(): void {
		wp_enqueue_script( 'lodash', '...' );
		wp_add_inline_script( 'lodash', 'window.lodash = _.noConflict();', 'after' );

		wp_enqueue_script(
			'furllery-script',
			plugins_url( 'assets/furllery.js', dirname( __FILE__ ) ),
			[ 'jquery' ],
			'1.0',
			true,
		);
	}

	protected function initialize_plugin(): Furllery {
		return $this->maybe_run_admin()->run_plugin();
	}

	protected function run_plugin(): Furllery {
		return $this->add_fields_to_media_library()->create_shortcodes()->add_actions();
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
				'input' => 'text',
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
				update_post_meta( $post['ID'], 'furllery_note', sanitize_text_field( $attachment['furllery_note'] ) );
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
		add_action( 'wp_enqueue_scripts', [ $this, 'add_furllery_js' ] );

		return $this;
	}

	protected function create_shortcodes(): Furllery {
		$this->create_gallery_shortcode();

		return $this;
	}

	protected function create_gallery_shortcode(): void {
		add_shortcode( 'furllery', function ( $attr ): string {
			$default = [ 'gallery' => - 1 ];
			$attr    = shortcode_atts( $default, $attr );

			return sprintf( '<div data-gallery="%d"></div>', (int) esc_attr( $attr['gallery'] ) );
		} );
	}

}