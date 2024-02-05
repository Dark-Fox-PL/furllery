<?php

final class FurlleryAdmin {
	public function initialize(): void {
		$this->add_actions();
	}

	public function create_admin_menu() {
		add_menu_page('Furllery', 'Furllery', 'manage_options', 'furllery', [ $this, 'plugin_main_page' ], 'dashicons-format-gallery');
		add_submenu_page( 'furllery', 'Furllery - Ustawienia', 'Ustawienia', 'manage_options', 'furllery__settings', [ $this, 'plugin_settings_page' ] );
		add_submenu_page( 'furllery', 'Furllery - Galerie', 'Galerie', 'manage_options', 'furllery__galleries', [ $this, 'plugin_galleries_page' ] );
	}

	public function plugin_main_page() {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__main.php';
	}

	public function plugin_settings_page() {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__settings.php';
	}

	public function plugin_galleries_page() {
		require_once DF__FURLLERY_VIEW_DIR . 'admin__galleries.php';
	}

	protected function add_actions(): FurlleryAdmin {
		add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );

		return $this;
	}

}