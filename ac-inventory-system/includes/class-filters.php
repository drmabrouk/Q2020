<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Filters {

	public static function get_all_tracking( $args = array() ) {
		global $wpdb;
		$table_tracking = $wpdb->prefix . 'ac_is_filter_tracking';
		$table_customers = $wpdb->prefix . 'ac_is_customers';
		$table_products = $wpdb->prefix . 'ac_is_products';

		$where = "1=1";
		if ( ! empty($args['status']) && $args['status'] === 'alert' ) {
			$where .= " AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
		}

		return $wpdb->get_results( "
			SELECT t.*, c.name as customer_name, c.phone as customer_phone, p.name as product_name
			FROM $table_tracking t
			JOIN $table_customers c ON t.customer_id = c.id
			JOIN $table_products p ON t.product_id = p.id
			WHERE $where
			ORDER BY t.expiry_date ASC
		" );
	}

	public static function replace_candle( $tracking_id ) {
		global $wpdb;
		$table_tracking = $wpdb->prefix . 'ac_is_filter_tracking';
		$table_logs = $wpdb->prefix . 'ac_is_filter_logs';

		$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_tracking WHERE id = %d", $tracking_id ) );
		if ( ! $item ) return false;

		// Calculate new expiry based on stage logic
		$validity = ($item->stage_number == 1) ? 3 : (($item->stage_number <= 3) ? 6 : 12);
		$new_expiry = date('Y-m-d', strtotime("+$validity months"));

		$wpdb->update( $table_tracking, array(
			'installation_date' => current_time('mysql'),
			'expiry_date'       => $new_expiry,
			'status'            => 'active'
		), array( 'id' => $tracking_id ) );

		$current_user = AC_IS_Auth::current_user();
		$operator_id = $current_user ? $current_user->id : 0;

		$wpdb->insert( $table_logs, array(
			'tracking_id' => $tracking_id,
			'action_type' => 'replacement',
			'operator_id' => $operator_id,
			'notes'       => sprintf( __( 'تم تغيير الشمعة رقم %d', 'ac-inventory-system' ), $item->stage_number )
		) );

		return true;
	}
}
