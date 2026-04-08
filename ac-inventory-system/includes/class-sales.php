<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Sales {

	public static function record_sale( $data ) {
		global $wpdb;
		$table_sales = $wpdb->prefix . 'ac_is_sales';
		$table_products = $wpdb->prefix . 'ac_is_products';

		// Start transaction
		$wpdb->query('START TRANSACTION');

		// Insert sale record
		$sale_data = array(
			'product_id'  => $data['product_id'],
			'quantity'    => $data['quantity'],
			'total_price' => $data['total_price'],
			'branch_id'   => $data['branch_id'],
			'operator_id' => get_current_user_id(),
			'sale_date'   => current_time('mysql'),
		);

		$inserted = $wpdb->insert( $table_sales, $sale_data );

		if ( ! $inserted ) {
			$wpdb->query('ROLLBACK');
			return false;
		}

		// Update stock
		$updated = $wpdb->query( $wpdb->prepare(
			"UPDATE $table_products SET stock_quantity = stock_quantity - %d WHERE id = %d AND stock_quantity >= %d",
			$data['quantity'], $data['product_id'], $data['quantity']
		) );

		if ( ! $updated ) {
			$wpdb->query('ROLLBACK');
			return false;
		}

		$wpdb->query('COMMIT');
		return $wpdb->insert_id;
	}
}
