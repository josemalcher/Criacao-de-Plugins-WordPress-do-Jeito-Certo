<?php

/**
 * Plugin Name:       MV SLIDER
 * Plugin URI:        https://josemalcher.net/wordpress
 * Description:       Estudos - Slider Simples
 * Version:           0.1
 * Requires at least: 5.8
 * Requires PHP:      7.2
 * Author:            José Malcher Jr
 * Author URI:        https://josemalcher.net
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mv-sliders
 * Domain Path:       /languages
 */


/*
MV-SLIDER is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

MV-SLIDER is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with MV-SLIDER. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	// die( 'not today' );
	exit;
}

if ( ! class_exists( 'MV_Slider' ) ) {
	class MV_Slider {

		public function __construct() {
			$this->define_constants();

			add_action( 'admin_menu', array( $this, 'add_menu' ) );

			require_once( MV_SLIDER_PATH . 'post-types/class.mv-slider-cpt.php' );
			$MV_Slider_Post_Type = new MV_Slider_Post_Type();

		}


		public function define_constants() {
			define( 'MV_SLIDER_PATH', plugin_dir_path( __FILE__ ) );
			define( 'MV_SLIDER_URL', plugin_dir_url( __FILE__ ) );
			define( 'MV_SLIDER_VERSION', '0.1.0' );
		}

		public static function activate() {
			// flush_rewrite_rules();
			update_option( 'rewrite_rules', '' );
			unregister_post_type( 'mv-slider' );
		}

		public static function deactivate() {
			flush_rewrite_rules();
		}

		public static function uninstall() {

		}

		public function add_menu() {
			add_menu_page(
				'MV Slider Options',
				'MV Slider',
				'manage_options',
				'mv_slider_admin',
				array( $this, 'mv_slider_settings_page' ),
				'dashicons-images-alt2'
			);
			add_submenu_page(
				'mv_slider_admin',
				'Manage Slides',
				'Manage Slides',
				'manage_options',
				'edit.php?post_type=mv-slider',
				null,
				null
			);

			add_submenu_page(
				'mv_slider_admin',
				'Add New Slide',
				'Add New Slide',
				'manage_options',
				'post-new.php?post_type=mv-slider',
				null,
				null
			);
		}

		public function mv_slider_settings_page() {
			require( MV_SLIDER_PATH . 'views/settings-page.php' );
		}
	}
}

if ( class_exists( 'MV_Slider' ) ) {

	register_activation_hook( __FILE__, array( 'MV_Slider', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'MV_Slider', 'deactivate' ) );
	register_uninstall_hook( __FILE__, array( 'MV_Slider', 'uninstall' ) );

	$mv_slider = new MV_Slider();
}
