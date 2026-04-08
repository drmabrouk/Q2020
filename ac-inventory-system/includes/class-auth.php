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
		return isset( $_SESSION['ac_is_user_id'] );
	}

	public static function current_user() {
		if ( ! self::is_logged_in() ) return null;
		return (object) array(
			'id'   => $_SESSION['ac_is_user_id'],
			'username' => $_SESSION['ac_is_username'],
			'role' => $_SESSION['ac_is_user_role'],
			'name' => $_SESSION['ac_is_user_name']
		);
	}

	public static function is_admin() {
		$user = self::current_user();
		return $user && $user->role === 'admin';
	}
}
