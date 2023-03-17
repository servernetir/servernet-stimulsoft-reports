<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

class Upload {

	public static $MIMES = [
		'mrt'      => 'text/xml',
		'mrt_json' => 'application/json',
	];


	public static function init() {

		add_filter( 'upload_mimes', [ Upload::class, 'register_allowed_mime_types' ], 999 );

	}


	/**
	 * Upload Mime Types
	 */
	public static function register_allowed_mime_types( $mimes = [] ) {

		if ( is_admin() && Report::is_report() ) {
			$mimes = [];
		}

		return array_merge( $mimes, self::$MIMES );
	}

}

Upload::init();