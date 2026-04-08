<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Database {

	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_products = $wpdb->prefix . 'ac_is_products';
		$table_sales    = $wpdb->prefix . 'ac_is_sales';
		$table_branches = $wpdb->prefix . 'ac_is_branches';

		$sql = "CREATE TABLE $table_branches (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			location text,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_products (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			category varchar(100),
			description text,
			original_price decimal(10,2) DEFAULT '0.00',
			discount decimal(10,2) DEFAULT '0.00',
			final_price decimal(10,2) DEFAULT '0.00',
			stock_quantity int DEFAULT 0,
			branch_id mediumint(9),
			image_url text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_sales (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			product_id mediumint(9) NOT NULL,
			quantity int NOT NULL,
			total_price decimal(10,2) NOT NULL,
			branch_id mediumint(9) NOT NULL,
			operator_id bigint(20) UNSIGNED NOT NULL,
			sale_date datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";

		if ( file_exists( ABSPATH . 'wp-admin/includes/upgrade.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}
}
