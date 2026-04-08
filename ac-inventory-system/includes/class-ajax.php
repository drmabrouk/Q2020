<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_ac_is_save_product', array( $this, 'save_product' ) );
		add_action( 'wp_ajax_ac_is_delete_product', array( $this, 'delete_product' ) );
		add_action( 'wp_ajax_ac_is_record_sale', array( $this, 'record_sale' ) );
		add_action( 'wp_ajax_ac_is_multi_sale', array( $this, 'multi_sale' ) );
		add_action( 'wp_ajax_ac_is_save_branch', array( $this, 'save_branch' ) );
		add_action( 'wp_ajax_ac_is_search_products', array( $this, 'search_products' ) );
		add_action( 'wp_ajax_ac_is_get_customer', array( $this, 'get_customer' ) );
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

	public function get_customer() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		$phone = sanitize_text_field( $_POST['phone'] );
		$customer = AC_IS_Customers::get_customer_by_phone( $phone );
		if ( $customer ) {
			wp_send_json_success( $customer );
		} else {
			wp_send_json_error( 'Not found' );
		}
	}

	public function multi_sale() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );

		global $wpdb;
		$items = $_POST['items'];
		$branch_id = intval( $_POST['branch_id'] );
		$customer_data = array(
			'name'    => sanitize_text_field( $_POST['customer_name'] ),
			'phone'   => sanitize_text_field( $_POST['customer_phone'] ),
			'address' => sanitize_text_field( $_POST['customer_address'] ),
			'email'   => sanitize_email( $_POST['customer_email'] ),
		);

		// Handle customer
		$customer = AC_IS_Customers::get_customer_by_phone( $customer_data['phone'] );
		if ( $customer ) {
			AC_IS_Customers::update_customer( $customer->id, $customer_data );
			$customer_id = $customer->id;
		} else {
			$customer_id = AC_IS_Customers::add_customer( $customer_data );
		}

		// Create Invoice
		$wpdb->insert( $wpdb->prefix . 'ac_is_invoices', array(
			'customer_id' => $customer_id,
			'total_amount' => floatval( $_POST['total_amount'] ),
			'branch_id'   => $branch_id,
			'operator_id' => get_current_user_id(),
		) );
		$invoice_id = $wpdb->insert_id;

		// Record individual sales
		foreach ( $items as $item ) {
			AC_IS_Sales::record_sale( array(
				'invoice_id'    => $invoice_id,
				'product_id'    => intval( $item['product_id'] ),
				'serial_number' => sanitize_text_field( $item['serial_number'] ),
				'quantity'      => intval( $item['quantity'] ),
				'total_price'   => floatval( $item['total_price'] ),
				'branch_id'     => $branch_id,
			) );
		}

		// Send Email if requested
		if ( ! empty( $_POST['send_email'] ) && ! empty( $customer_data['email'] ) ) {
			$this->send_invoice_email( $invoice_id, $customer_data['email'] );
		}

		wp_send_json_success( array( 'invoice_id' => $invoice_id ) );
	}

	private function send_invoice_email( $invoice_id, $to ) {
		$invoice = AC_IS_Sales::get_invoice( $invoice_id );
		$items = AC_IS_Sales::get_invoice_items( $invoice_id );

		$subject = sprintf( __( 'فاتورة مبيعات رقم #%d - %s', 'ac-inventory-system' ), $invoice_id, get_bloginfo( 'name' ) );

		$message = "<h2>" . __( 'شكراً لتعاملكم معنا', 'ac-inventory-system' ) . "</h2>";
		$message .= "<p>" . __( 'رقم الفاتورة:', 'ac-inventory-system' ) . " #" . $invoice_id . "</p>";
		$message .= "<p>" . __( 'الإجمالي:', 'ac-inventory-system' ) . " " . number_format($invoice->total_amount, 2) . " EGP</p>";
		$message .= "<h3>" . __( 'المنتجات:', 'ac-inventory-system' ) . "</h3><ul>";

		foreach ( $items as $item ) {
			$message .= "<li>" . esc_html( $item->product_name ) . " (" . $item->quantity . ") - " . number_format($item->total_price, 2) . " EGP</li>";
		}
		$message .= "</ul>";

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $to, $subject, $message, $headers );
	}
}

new AC_IS_Ajax();
