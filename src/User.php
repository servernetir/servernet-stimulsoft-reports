<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

class User {

	public static function init() {

		add_action( 'wp_login', [ __CLASS__, 'check_user_access' ], 999 );
		add_action( 'admin_init', [ __CLASS__, 'check_user_access' ], 1 );

	}


	public static function get_all_roles( $site_id = null ) {

		if ( ! $site_id ) {
			$site_id = get_current_blog_id();
		}

		if ( is_multisite() && get_current_blog_id() != $site_id ) {
			switch_to_blog( $site_id );
			$role_names = wp_roles()->get_names();
			restore_current_blog();
		} else {
			$role_names = wp_roles()->get_names();
		}

		asort( $role_names );

		return $role_names;
	}


	public static function get_reports( $user_id = null ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		return get_posts( [
			'nopaging'         => true,
			'suppress_filters' => false,
			'post_type'        => PostType::$POST_TYPE,
			'meta_key'         => Report::get_meta_key( 'users' ),
			'meta_value'       => $user_id,
		] );
	}


	public static function has_access( $report_id, $user_id = null ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$user_id = (int) $user_id;

		if ( empty( $report_id ) ) {
			return false;
		}

		$report = get_post( $report_id );

		if ( is_wp_error( $report ) || empty( $report ) ) {
			return false;
		}

		return in_array( $user_id, Report::get_users( $report_id ) ) ||
		       (int) $report->post_author === $user_id ||
		       is_super_admin();
	}


	public static function check_user_access() {

		if ( is_super_admin() ) {
			return;
		}

		$redirect = false;
		$roles    = Settings::get_roles();
		$user     = wp_get_current_user();

		if ( $user->roles ) {

			foreach ( $user->roles as $role ) {

				if ( in_array( $role, $roles ) ) {
					$redirect = true;
					break;
				}

			}

		}

		if ( $redirect ) {

			self::redirect_to_reports_page();

		}

	}


	public static function redirect_to_reports_page() {

		$reports = self::get_reports();
		$url     = home_url();

		if ( ! empty( $reports ) ) {
			$url = get_permalink( reset( $reports ) );
		}

		wp_redirect( $url );
		exit();

	}


}

User::init();