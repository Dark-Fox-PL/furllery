<?php

final class FurlleryAdmin {
	public function initialize(): void {
		$this->add_actions();
	}

	public function add_admin_css(): void {
		wp_enqueue_style( 'furllery-admin-style', plugins_url( 'assets/admin.css', dirname( __FILE__ ) ) );
	}
	public function add_admin_js(): void {
		wp_enqueue_script( 'furllery-admin-script', plugins_url( 'assets/admin.js', dirname( __FILE__ ) ) );
		wp_enqueue_script( 'furllery-admin-script', get_template_directory_uri() . '/js/admin.js', array( 'jquery', 'wp-mediaelement' ), '1.0.0', true );
		wp_enqueue_media();
	}

	public function create_admin_menu(): void {
		add_menu_page('Furllery', 'Furllery', 'manage_options', 'furllery', [ $this, 'plugin_main_page' ], 'dashicons-format-gallery');
		add_submenu_page( 'furllery', 'Furllery - Dodaj galerię', 'Dodaj Galerię', 'manage_options', 'furllery__add_gallery', [ $this, 'plugin_add_gallery_page' ] );
		add_submenu_page( 'furllery', 'Furllery - Ustawienia', 'Ustawienia', 'manage_options', 'furllery__settings', [ $this, 'plugin_settings_page' ] );
	}

	public function extend_top_bar_menu( $wp_admin_bar ): void {
		$args = array(
			'id'    => 'furllery-add',
			'parent' => 'new-content',
			'title' => 'Galerię Furllery',
			'href'  => admin_url() . 'admin.php?page=furllery__add_gallery',
		);
		$wp_admin_bar->add_node( $args );
	}

	public function plugin_main_page(): void {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__main.php';
	}

	public function plugin_settings_page(): void {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__settings.php';
	}

	public function plugin_galleries_page(): void {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__galleries.php';
	}

	public function plugin_add_gallery_page(): void {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__add_gallery.php';
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