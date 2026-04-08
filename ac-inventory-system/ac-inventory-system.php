<?php
/**
 * Plugin Name: AC Inventory System
 * Description: An internal inventory and sales management system for AC units, cooling systems, and water filters with an Arabic RTL interface.
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: ac-inventory-system
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'AC_IS_VERSION', '1.0.0' );
define( 'AC_IS_PATH', plugin_dir_path( __FILE__ ) );
define( 'AC_IS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Class
 */
class AC_Inventory_System {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	private function define_constants() {
		// Already defined above for now, but can move more here if needed.
	}

	private function includes() {
		require_once AC_IS_PATH . 'includes/class-database.php';
		require_once AC_IS_PATH . 'includes/class-inventory.php';
		require_once AC_IS_PATH . 'includes/class-sales.php';
		require_once AC_IS_PATH . 'includes/class-shortcode.php';
		require_once AC_IS_PATH . 'includes/class-ajax.php';
		require_once AC_IS_PATH . 'includes/class-reports.php';
	}

	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'AC_IS_Database', 'create_tables' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets() {
		wp_enqueue_media();
		wp_enqueue_style( 'ac-is-rtl-style', AC_IS_URL . 'assets/css/style-rtl.css', array(), AC_IS_VERSION );
		wp_enqueue_style( 'ac-is-print-style', AC_IS_URL . 'assets/css/print.css', array(), AC_IS_VERSION, 'print' );

		// Enqueue JsBarcode from CDN
		wp_enqueue_script( 'jsbarcode', 'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js', array(), '3.11.5', true );

		// Enqueue Html5Qrcode for camera scanning
		wp_enqueue_script( 'html5-qrcode', 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js', array(), '2.3.8', true );

		wp_enqueue_script( 'ac-is-scripts', AC_IS_URL . 'assets/js/scripts.js', array( 'jquery', 'jsbarcode', 'html5-qrcode' ), AC_IS_VERSION, true );

		wp_localize_script( 'ac-is-scripts', 'ac_is_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ac_is_nonce' ),
		) );
	}
}

function AC_IS() {
	return AC_Inventory_System::get_instance();
}

// Kick off the plugin
AC_IS();
