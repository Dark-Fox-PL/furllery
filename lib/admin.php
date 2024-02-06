<?php

final class FurlleryAdmin {
	public function initialize(): void {
		$this->add_actions();
	}

	public function add_admin_css(): void {
		wp_enqueue_style( 'my-plugin-admin-style', plugins_url( 'assets/admin.css', dirname( __FILE__ ) ) );
	}

	public function create_admin_menu(): void {
		add_menu_page('Furllery', 'Furllery', 'manage_options', 'furllery', [ $this, 'plugin_main_page' ], 'dashicons-format-gallery');
		add_submenu_page( 'furllery', 'Furllery - Ustawienia', 'Ustawienia', 'manage_options', 'furllery__settings', [ $this, 'plugin_settings_page' ] );
		add_submenu_page( 'furllery', 'Furllery - Galerie', 'Galerie', 'manage_options', 'furllery__galleries', [ $this, 'plugin_galleries_page' ] );
		add_submenu_page( 'furllery__galleries', 'Furllery - Dodaj galerię', 'Dodaj Galerię', 'manage_options', 'furllery__add_gallery', [ $this, 'plugin_add_gallery_page' ] );
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

		return $this;
	}

}