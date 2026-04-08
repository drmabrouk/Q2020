<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_ac_is_save_product', array( $this, 'save_product' ) );
		add_action( 'wp_ajax_ac_is_delete_product', array( $this, 'delete_product' ) );
		add_action( 'wp_ajax_ac_is_record_sale', array( $this, 'record_sale' ) );
		add_action( 'wp_ajax_ac_is_save_branch', array( $this, 'save_branch' ) );
		add_action( 'wp_ajax_ac_is_search_products', array( $this, 'search_products' ) );
	}

	public function save_product() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		$data = array(
			'name'           => sanitize_text_field( $_POST['name'] ),
			'category'       => sanitize_text_field( $_POST['category'] ),
			'subcategory'    => sanitize_text_field( $_POST['subcategory'] ),
			'original_price' => floatval( $_POST['original_price'] ),
			'discount'       => floatval( $_POST['discount'] ),
			'final_price'    => floatval( $_POST['final_price'] ),
			'stock_quantity' => intval( $_POST['stock_quantity'] ),
			'branch_id'      => intval( $_POST['branch_id'] ),
			'image_url'      => esc_url_raw( $_POST['image_url'] ),
			'serial_number'  => sanitize_text_field( $_POST['serial_number'] ),
			'barcode'        => sanitize_text_field( $_POST['barcode'] ),
		);

		if ( $id ) {
			AC_IS_Inventory::update_product( $id, $data );
		} else {
			AC_IS_Inventory::add_product( $data );
		}

		wp_send_json_success( 'Product saved' );
	}

	public function delete_product() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$id = intval( $_POST['id'] );
		AC_IS_Inventory::delete_product( $id );
		wp_send_json_success( 'Product deleted' );
	}

	public function record_sale() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$data = array(
			'product_id'    => intval( $_POST['product_id'] ),
			'serial_number' => sanitize_text_field( $_POST['serial_number'] ),
			'quantity'      => intval( $_POST['quantity'] ),
			'total_price'   => floatval( $_POST['total_price'] ),
			'branch_id'     => intval( $_POST['branch_id'] ),
		);

		$sale_id = AC_IS_Sales::record_sale( $data );

		if ( $sale_id ) {
			wp_send_json_success( array( 'sale_id' => $sale_id ) );
		} else {
			wp_send_json_error( 'Failed to record sale' );
		}
	}

	public function save_branch() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$data = array(
			'name'     => sanitize_text_field( $_POST['name'] ),
			'location' => sanitize_textarea_field( $_POST['location'] ),
		);

		AC_IS_Inventory::add_branch( $data );
		wp_send_json_success( 'Branch saved' );
	}

	public function search_products() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );

		$args = array(
			'search'   => sanitize_text_field( $_POST['search'] ),
			'category' => sanitize_text_field( $_POST['category'] ),
		);

		$products = AC_IS_Inventory::get_products( $args );
		wp_send_json_success( $products );
	}
}

new AC_IS_Ajax();
