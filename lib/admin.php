<?php

final class FurlleryAdmin {
	public function __construct( protected FurlleryDB $db = new FurlleryDB ) { }

	public function initialize(): void {
		$this->add_actions();
	}

	public function add_admin_css(): void {
		wp_enqueue_style( 'furllery-admin-style', plugins_url( 'assets/admin.css', dirname( __FILE__ ) ) );
	}

	public function add_admin_js(): void {
		wp_enqueue_script( 'lodash', '...' );
		wp_add_inline_script( 'lodash', 'window.lodash = _.noConflict();', 'after' );

		wp_enqueue_script( 'furllery-admin-script', plugins_url( 'assets/admin.js', dirname( __FILE__ ) ) );
		wp_enqueue_script( 'furllery-admin-script', get_template_directory_uri() . '/js/admin.js', [
			'jquery',
			'wp-mediaelement',
		], '1.0.0', true );
		wp_enqueue_media();
	}

	public function create_admin_menu(): void {
		add_menu_page( 'Furllery', 'Furllery', 'manage_options', 'furllery', [
			$this,
			'plugin_main_page',
		], 'dashicons-format-gallery' );

		$menu_title = ! empty( $_GET['edit_id'] ) ? 'Edytuj Galerię' : 'Dodaj Galerię';
		add_submenu_page( 'furllery', 'Furllery - ' . $menu_title, $menu_title, 'manage_options', 'furllery__upsert_gallery', [
			$this,
			'plugin_upsert_gallery_page',
		] );

		add_submenu_page( 'furllery', 'Furllery - Ustawienia', 'Ustawienia', 'manage_options', 'furllery__settings', [
			$this,
			'plugin_settings_page',
		] );
	}

	public function extend_top_bar_menu( $wp_admin_bar ): void {
		$args = [
			'id'     => 'furllery-add',
			'parent' => 'new-content',
			'title'  => 'Galerię Furllery',
			'href'   => admin_url() . 'admin.php?page=admin__upsert_gallery',
		];
		$wp_admin_bar->add_node( $args );
	}

	public function plugin_main_page(): void {
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_gallery' && isset( $_GET['gallery_id'] ) ) {
			$gallery_id = intval( $_GET['gallery_id'] );
			$removed = $this->db->delete_gallery( $gallery_id );

			$query_args   = [
				'action'  => 'delete_gallery',
				'success' => $removed,
			];
			$redirect_url = add_query_arg( $query_args, admin_url( 'admin.php?page=furllery' ) );

			wp_safe_redirect( $redirect_url );
			exit;
		}

		require_once DF__FURLLERY_VIEW_DIR . 'admin__main.php';
	}

	public function plugin_settings_page(): void {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__settings.php';
	}

	public function plugin_upsert_gallery_page(): void {
		global $furllery_errors;

		$gallery_data = [];
		$images_urls  = [];

		if ( ! empty( $_GET['edit_id'] ) ) {
			global $wpdb;

			$gallery_id   = intval( $_GET['edit_id'] );
			$table_name   = $wpdb->prefix . FurlleryDB::TABLE_GALLERIES;
			$gallery_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $gallery_id ), ARRAY_A );

			$images = json_decode( $gallery_data['content'], true );

			if ( is_array( $images ) && 0 < count( $images ) ) {
				foreach ( $images as $image_id ) {
					$image_url = wp_get_attachment_image_src( $image_id );

					if ( $image_url ) {
						$images_urls[ $image_id ] = $image_url[0];
					}
				}
			}

			$this->db->maybe_update_gallery( $gallery_id );
		} else {
			$this->db->maybe_insert_gallery();
		}

		require_once DF__FURLLERY_VIEW_DIR . 'admin__upsert_gallery.php';
	}

	protected function add_actions(): FurlleryAdmin {
		add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_css' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_js' ] );
		add_action( 'admin_bar_menu', [ $this, 'extend_top_bar_menu' ] );

		return $this->add_admin_ajax_actions();
	}

	protected function add_admin_ajax_actions(): FurlleryAdmin {
		$open_ml = function () {
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_die();
			}
		};

		add_action( 'wp_ajax_furllery_open_media_library', $open_ml );

		return $this;
	}

}