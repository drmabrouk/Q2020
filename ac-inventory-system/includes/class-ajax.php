<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Ajax {

	public function __construct() {
		$actions = array(
			'save_product', 'delete_product', 'record_sale', 'multi_sale',
			'save_branch', 'search_products', 'get_customer', 'delete_invoice',
			'logout', 'record_attendance', 'add_staff', 'delete_staff', 'save_settings'
		);

		foreach ( $actions as $action ) {
			add_action( 'wp_ajax_ac_is_' . $action, array( $this, $action ) );
			add_action( 'wp_ajax_nopriv_ac_is_' . $action, array( $this, $action ) );
		}

		add_action( 'wp_ajax_nopriv_ac_is_login', array( $this, 'login' ) );
		add_action( 'wp_ajax_ac_is_login', array( $this, 'login' ) );
	}

	public function save_product() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::is_admin() ) wp_send_json_error( 'Unauthorized' );

		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		$data = array(
			'name'           => sanitize_text_field( $_POST['name'] ),
			'category'       => sanitize_text_field( $_POST['category'] ),
			'subcategory'    => sanitize_text_field( $_POST['subcategory'] ),
			'original_price' => floatval( $_POST['original_price'] ),
			'purchase_cost'  => floatval( $_POST['purchase_cost'] ),
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
		if ( ! AC_IS_Auth::is_admin() ) wp_send_json_error( 'Unauthorized' );

		$id = intval( $_POST['id'] );
		AC_IS_Inventory::delete_product( $id );
		wp_send_json_success( 'Product deleted' );
	}

	public function record_sale() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::is_logged_in() ) wp_send_json_error( 'Unauthorized' );

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

	public function login() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		$username = sanitize_text_field( $_POST['username'] );
		$password = $_POST['password'];

		if ( AC_IS_Auth::login( $username, $password ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	public function record_attendance() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::is_manager() ) wp_send_json_error( 'Unauthorized' );

		$data = array(
			'staff_id'  => intval( $_POST['staff_id'] ),
			'work_date' => sanitize_text_field( $_POST['work_date'] ),
			'check_in'  => sanitize_text_field( $_POST['check_in'] ),
			'check_out' => sanitize_text_field( $_POST['check_out'] ),
			'status'    => sanitize_text_field( $_POST['status'] ),
		);

		AC_IS_Payroll::record_attendance( $data );
		wp_send_json_success();
	}

	public function logout() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		AC_IS_Auth::logout();
		wp_send_json_success();
	}

	public function add_staff() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::is_admin() ) wp_send_json_error( 'Unauthorized' );

		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_staff';

		$data = array(
			'username'      => sanitize_text_field( $_POST['staff_username'] ),
			'password'      => password_hash( $_POST['staff_password'], PASSWORD_DEFAULT ),
			'name'          => sanitize_text_field( $_POST['staff_name'] ),
			'role'          => sanitize_text_field( $_POST['staff_role'] ),
			'base_salary'   => floatval( $_POST['base_salary'] ),
			'working_days'  => intval( $_POST['working_days'] ),
			'working_hours' => intval( $_POST['working_hours'] ),
		);

		$wpdb->insert( $table, $data );
		wp_send_json_success();
	}

	public function delete_staff() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::is_admin() ) wp_send_json_error( 'Unauthorized' );

		$id = intval( $_POST['id'] );
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'ac_is_staff', array( 'id' => $id ) );
		wp_send_json_success();
	}

	public function save_settings() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::is_admin() ) wp_send_json_error( 'Unauthorized' );

		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_settings';

		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'ac_is_' ) === false && $key !== 'action' && $key !== 'nonce' ) {
				$wpdb->replace( $table, array(
					'setting_key'   => sanitize_key( $key ),
					'setting_value' => sanitize_text_field( $value )
				) );
			}
		}

		wp_send_json_success();
	}

	public function delete_invoice() {
		check_ajax_referer( 'ac_is_nonce', 'nonce' );
		if ( ! AC_IS_Auth::can_delete_records() ) wp_send_json_error( 'Unauthorized' );

		$invoice_id = intval( $_POST['invoice_id'] );
		AC_IS_Sales::delete_invoice( $invoice_id );
		wp_send_json_success();
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
		if ( ! AC_IS_Auth::is_logged_in() ) wp_send_json_error( 'Unauthorized' );
		if ( ! AC_IS_Auth::is_logged_in() ) wp_send_json_error( 'Unauthorized' );
		if ( ! AC_IS_Auth::is_logged_in() ) wp_send_json_error( 'Unauthorized' );

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

		$current_user = AC_IS_Auth::current_user();
		$operator_id  = $current_user ? $current_user->id : 0;

		// Create Invoice
		$wpdb->insert( $wpdb->prefix . 'ac_is_invoices', array(
			'customer_id' => $customer_id,
			'total_amount' => floatval( $_POST['total_amount'] ),
			'branch_id'   => $branch_id,
			'operator_id' => $operator_id,
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
