<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Inventory {

	public static function get_products( $args = array() ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_products';
		
		$where = "1=1";
		if ( ! empty( $args['branch_id'] ) ) {
			$where .= $wpdb->prepare( " AND branch_id = %d", $args['branch_id'] );
		}
		
		return $wpdb->get_results( "SELECT * FROM $table WHERE $where ORDER BY created_at DESC" );
	}

	public static function get_product( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_products';
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
	}

	public static function add_product( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_products';
		return $wpdb->insert( $table, $data );
	}

	public static function update_product( $id, $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_products';
		return $wpdb->update( $table, $data, array( 'id' => $id ) );
	}

	public static function delete_product( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_products';
		return $wpdb->delete( $table, array( 'id' => $id ) );
	}

	public static function get_branches() {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_branches';
		return $wpdb->get_results( "SELECT * FROM $table" );
	}

	public static function add_branch( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_branches';
		return $wpdb->insert( $table, $data );
	}
}
