<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Reports {

	public static function get_daily_sales() {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_sales';
		return $wpdb->get_results( "SELECT DATE(sale_date) as date, SUM(total_price) as total FROM $table GROUP BY DATE(sale_date) ORDER BY date DESC LIMIT 7" );
	}

	public static function get_weekly_sales() {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_sales';
		return $wpdb->get_results( "SELECT YEARWEEK(sale_date) as week, SUM(total_price) as total FROM $table GROUP BY YEARWEEK(sale_date) ORDER BY week DESC LIMIT 4" );
	}

	public static function get_stock_overview() {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_products';
		return $wpdb->get_results( "SELECT name, stock_quantity FROM $table WHERE stock_quantity < 10 ORDER BY stock_quantity ASC" );
	}

	public static function export_sales_csv() {
		if ( ! isset( $_GET['ac_export'] ) || $_GET['ac_export'] !== 'sales' ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $wpdb;
		$table_sales = $wpdb->prefix . 'ac_is_sales';
		$table_products = $wpdb->prefix . 'ac_is_products';
		$sales = $wpdb->get_results("
			SELECT s.sale_date, p.name as product_name, s.quantity, s.total_price 
			FROM $table_sales s
			JOIN $table_products p ON s.product_id = p.id
			ORDER BY s.sale_date DESC
		");

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=sales_report.csv');
		$output = fopen('php://output', 'w');
		fputcsv($output, array('Date', 'Product', 'Quantity', 'Total Price'));
		foreach ($sales as $sale) {
			fputcsv($output, (array)$sale);
		}
		fclose($output);
		exit;
	}
}

add_action( 'init', array( 'AC_IS_Reports', 'export_sales_csv' ) );
