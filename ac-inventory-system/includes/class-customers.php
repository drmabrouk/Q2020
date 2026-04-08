<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Customers {

	public static function get_customer_by_phone( $phone ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_customers';
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE phone = %s", $phone ) );
	}

	public static function add_customer( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_customers';
		$wpdb->insert( $table, $data );
		return $wpdb->insert_id;
	}

	public static function update_customer( $id, $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_customers';
		return $wpdb->update( $table, $data, array( 'id' => $id ) );
	}

	public static function get_customer( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_customers';
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
	}
}
