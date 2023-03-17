<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

/**
 * Register Ajax Actions
 */
class Ajax {

	private static $ACTIONS = [
		'saveSettings' => [ __CLASS__, 'save_settings' ],
		'searchUsers'  => [ __CLASS__, 'search_users' ],
	];


	public static function init() {

		foreach ( Ajax::get_actions() as $action => $callback ) {

			add_action( 'wp_ajax_' . $action, $callback );

		}

	}


	public static function js_vars( $handle, $args = [] ) {

		// JS Vars
		wp_localize_script( $handle, 'FW_STIMULSOFT', wp_parse_args( $args, [
			'ajaxNonce' => Ajax::get_nonce(),
			'actions'   => Ajax::get_actions( false ),
		] ) );

	}


	/**
	 * Ajax Actions
	 *
	 * @param  bool  $with_callback  if set true rerun with action as key and php callback as value
	 *
	 * @return array
	 */
	public static function get_actions( $with_callback = true ) {

		$actions = [];
		$data    = self::$ACTIONS;

		foreach ( $data as $key => $value ) {

			if ( $with_callback ) {
				$action   = Helper::suffix( $key );
				$callback = $value;
			} else {
				$action   = $key;
				$callback = Helper::suffix( $key );
			}

			$actions[ $action ] = $callback;
		}

		return $actions;
	}


	/**
	 * Ajax Nonce
	 *
	 * @return string
	 */
	public static function get_nonce_action() {

		return Helper::suffix( 'ajax_security' );
	}


	public static function get_nonce() {

		return wp_create_nonce( self::get_nonce_action() );
	}


	public static function check_nonce() {

		return check_ajax_referer( self::get_nonce_action() );
	}


	/**
	 * Save Settings
	 *
	 * @return void
	 */
	public static function save_settings() {

		self::check_nonce();

		$option_id = Settings::get_option_id();

		Settings::$options = wp_parse_args(
			$_POST[ $option_id ] ?? [],
			Settings::get()
		);

		update_option( $option_id, Settings::$options );

		wp_send_json_success();
	}


	/**
	 * Search Users
	 *
	 * @return void
	 */
	public static function search_users() {

		self::check_nonce();

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Access Denied!' );
		}

		$search = $_POST['search'] ?? '';
		$search = wp_unslash( $search );

		if ( empty( $search ) ) {
			wp_send_json_success( [] );
		}

		/**
		 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/
		 */
		$users = get_users( [
			'number'         => 10,
			'count_total'    => false,
			'search'         => "*{$search}*",
			'exclude'        => get_current_user_id(),
			'search_columns' => [ 'ID', 'user_login', 'user_email', 'user_nicename', 'display_name' ],
			'fields'         => [ 'ID', 'user_email', 'display_name' ],
		] );

		if ( is_wp_error( $users ) || empty( $users ) ) {
			$users = [];
		}

		wp_send_json_success( $users );

	}

}

Ajax::init();
