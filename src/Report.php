<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

class Report {

	public static function is_report( $report_id = null ) {

		return get_post_type( $report_id ) === PostType::$POST_TYPE;
	}


	public static function get_meta_key( $meta_key ) {

		return "fw_report_{$meta_key}";
	}


	/**
	 * Get MRT File URL
	 *
	 * @param $report_id
	 *
	 * @return mixed
	 */

	public static function get_mrt_file_url( $report_id ) {

		return wp_get_attachment_url( self::get_mrt_file_id( $report_id ) );
	}


	public static function get_mrt_file_id( $report_id ) {

		return self::get_meta( $report_id, 'file_id' );
	}


	public static function set_mrt_file_id( $report_id, $file_id = null ) {

		if ( empty( $file_id ) ) {
			return self::delete_meta( $report_id, 'file_id' );
		}

		return self::set_meta( $report_id, 'file_id', $file_id );
	}


	/**
	 * Get Report Users
	 */
	public static function get_users( $report_id ) {

		return self::get_meta( $report_id, 'users', false ) ?: [];
	}


	public static function set_users( $report_id, $users = [] ) {

		$users = (array) $users;
		$users = array_filter( $users );
		$users = array_map( 'absint', $users );

		if ( empty( $users ) ) {
			return self::delete_meta( $report_id, 'users' );
		}

		return self::set_meta( $report_id, 'users', $users, false );
	}


	/**
	 * Handle Report Meta
	 *
	 * @param  int     $report_id
	 * @param  string  $meta_key
	 * @param  bool    $single
	 *
	 * @return mixed
	 */
	private static function get_meta( $report_id, $meta_key, $single = true ) {

		return get_post_meta( $report_id, self::get_meta_key( $meta_key ), $single );
	}


	private static function set_meta( $report_id, $meta_key, $meta_value, $single = true ) {

		if ( $single === false ) {
			self::delete_meta( $report_id, $meta_key );
		}

		$meta_key = self::get_meta_key( $meta_key );

		if ( $single ) {
			return update_post_meta( $report_id, $meta_key, $meta_value );
		}

		$meta_value = (array) $meta_value;

		foreach ( $meta_value as $item ) {
			add_post_meta( $report_id, $meta_key, $item, false );
		}

		return true;
	}


	private static function delete_meta( $report_id, $meta_key ) {

		return delete_post_meta( $report_id, self::get_meta_key( $meta_key ) );
	}


}