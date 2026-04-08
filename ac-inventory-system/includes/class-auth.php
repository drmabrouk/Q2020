<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_Auth {

	public static function init() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
	}

	public static function login( $username, $password ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ac_is_staff';

		$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE username = %s", $username ) );

		if ( $user && password_verify( $password, $user->password ) ) {
			$_SESSION['ac_is_user_id']   = $user->id;
			$_SESSION['ac_is_username']  = $user->username;
			$_SESSION['ac_is_user_role'] = $user->role;
			$_SESSION['ac_is_user_name'] = $user->name;
			return true;
		}
		return false;
	}

	public static function logout() {
		unset( $_SESSION['ac_is_user_id'] );
		unset( $_SESSION['ac_is_username'] );
		unset( $_SESSION['ac_is_user_role'] );
		unset( $_SESSION['ac_is_user_name'] );
	}

	public static function is_logged_in() {
		// Auto-grant access to WordPress Administrators
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return isset( $_SESSION['ac_is_user_id'] );
	}

	public static function current_user() {
		// If WP admin, return WP user data mapped to staff format
		if ( current_user_can( 'manage_options' ) ) {
			$wp_user = wp_get_current_user();
			return (object) array(
				'id'   => 'wp_' . $wp_user->ID,
				'username' => $wp_user->user_login,
				'role' => 'admin',
				'name' => $wp_user->display_name
			);
		}

		if ( ! isset( $_SESSION['ac_is_user_id'] ) ) return null;

		return (object) array(
			'id'   => $_SESSION['ac_is_user_id'],
			'username' => $_SESSION['ac_is_username'],
			'role' => $_SESSION['ac_is_user_role'],
			'name' => $_SESSION['ac_is_user_name']
		);
	}

	public static function is_admin() {
		// WP Admin always has plugin admin privileges
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		$user = self::current_user();
		return $user && $user->role === 'admin';
	}
}
