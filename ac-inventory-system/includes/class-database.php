<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Database {

	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_products  = $wpdb->prefix . 'ac_is_products';
		$table_sales     = $wpdb->prefix . 'ac_is_sales';
		$table_branches  = $wpdb->prefix . 'ac_is_branches';
		$table_customers = $wpdb->prefix . 'ac_is_customers';
		$table_invoices  = $wpdb->prefix . 'ac_is_invoices';

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
			subcategory varchar(100),
			description text,
			original_price decimal(10,2) DEFAULT '0.00',
			discount decimal(10,2) DEFAULT '0.00',
			final_price decimal(10,2) DEFAULT '0.00',
			stock_quantity int DEFAULT 0,
			branch_id mediumint(9),
			image_url text,
			serial_number varchar(255),
			barcode varchar(255),
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_customers (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			phone varchar(50) NOT NULL,
			address text,
			email varchar(255),
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_invoices (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			customer_id mediumint(9),
			total_amount decimal(10,2) NOT NULL,
			branch_id mediumint(9) NOT NULL,
			operator_id bigint(20) UNSIGNED NOT NULL,
			invoice_date datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_sales (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			invoice_id mediumint(9) NOT NULL,
			product_id mediumint(9) NOT NULL,
			serial_number varchar(255),
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
