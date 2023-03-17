<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

class Helper {

	public static function suffix( $key ) {

		return 'fw_stimulsoft_' . $key;
	}


	public static function path( $path = null ) {

		return path_join( plugin_dir_path( FW_STIMULSOFT_FILE ), $path );
	}


	public static function url( $path = null ) {

		return path_join( plugin_dir_url( FW_STIMULSOFT_FILE ), $path );
	}


	public static function wp_enqueue_script( $handle, $src = '', $deps = [], $ver = false, $in_footer = false ) {

		$ver = self::get_enqueue_version( $src, $ver );
		$src = self::url() . $src;

		wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );

	}


	public static function wp_enqueue_style( $handle, $src = '', $deps = [], $ver = false, $media = 'all' ) {

		$ver = self::get_enqueue_version( $src, $ver );
		$src = self::url() . $src;

		wp_enqueue_style( $handle, $src, $deps, $ver, $media );

	}


	public static function get_enqueue_version( $src, $ver = false ) {

		if ( ! strlen( $ver ) ) {
			$path = self::path() . $src;

			if ( file_exists( $path ) ) {
				$ver = filemtime( $path );
			}
		}

		return $ver;
	}


}