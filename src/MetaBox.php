<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;


class MetaBox {

	public static function init() {

		/**
		 * Register Report Metabox
		 */
		add_action( 'add_meta_boxes', [ MetaBox::class, 'register_metabox' ] );
		add_action( 'save_post', [ MetaBox::class, 'save_metabox' ] );

		/**
		 * Metabox CSS & JS
		 */
		add_action( 'admin_enqueue_scripts', [ MetaBox::class, 'css_js' ], 1 );

	}


	/**
	 * Metabox CSS & JS
	 *
	 * @return void
	 */
	public static function css_js() {

		if ( ! Report::is_report() ) {
			return;
		}

		// Wordpress Upload Frame
		wp_enqueue_media();

		// Select2
		Helper::wp_enqueue_style( 'select2', 'assets/select2/css/select2.min.css' );
		Helper::wp_enqueue_script( 'select2', 'assets/select2/js/select2.min.js' );
		Helper::wp_enqueue_script( 'select2-i18n', sprintf( 'assets/select2/js/i18n/%s.js', is_rtl() ? 'fa' : 'en' ) );

		// Metabox JS & CSS
		$handle = Helper::suffix( 'fw_report_metabox' );
		Helper::wp_enqueue_script( $handle, 'assets/metabox.js', [ 'wp-util', 'jquery', 'select2' ] );
		Helper::wp_enqueue_style( $handle, 'assets/metabox.css' );

		Ajax::js_vars( $handle);

	}


	/**
	 * Save Metabox options
	 *
	 * @param $report_id
	 *
	 * @return void
	 */
	public static function save_metabox( $report_id ) {

		if ( empty( $report_id ) ) {
			return;
		}

		$file_id = $_REQUEST['fw_report_file_id'] ?? false;
		$users   = $_REQUEST['fw_report_users'] ?? false;

		Report::set_mrt_file_id( $report_id, $file_id );
		Report::set_users( $report_id, $users );

	}


	/**
	 * Register Metabox
	 *
	 * @return void
	 */
	public static function register_metabox() {

		add_meta_box(
			$id = 'report_options',
			$title = 'تنظیمات',
			$callback = [ __CLASS__, 'display_metabox' ],
			$screen = PostType::$POST_TYPE,
			$context = 'advanced',
			$priority = 'default',
			$callback_args = null
		);

	}


	public static function display_metabox( \WP_Post $post ) {

		if ( empty( $post ) ) {
			return;
		}

		$report_id = $post->ID;

		$file_id  = Report::get_mrt_file_id( $report_id );
		$file_url = Report::get_mrt_file_url( $report_id );
		$users    = Report::get_users( $report_id );

		$upload_mimes      = Upload::$MIMES;
		$upload_mimes_type = implode( ', ', array_keys( $upload_mimes ) );

		?>

        <table class="form-table" data-post-id="<?= esc_attr( $report_id ) ?>">
            <tbody>
                <tr>
                    <th>
                        <label for="report_upload_file_btn">آپلود فایل گزارش</label>
                    </th>
                    <td>
                        <input
                                type="hidden"
                                name="fw_report_file_id"
                                id="fw_report_file_id"
                                value="<?= esc_attr( $file_id ) ?>"
                        >
                        <button
                                type="button"
                                data-upload-btn
                                data-upload-mimes="<?= esc_attr( json_encode( $upload_mimes ) ) ?>"
                                data-upload-input="#fw_report_file_id"
                                data-upload-preview="#fw_report_file_preview"
                                data-upload-postid="<?= esc_attr( $report_id ) ?>"
                                id="report_upload_file_btn"
                                class="button button-small button-secondary"><?= __( 'Upload' ) ?></button>
                        <span>
                            فرمت مجاز: <?= $upload_mimes_type ?>
                        </span>
                        <br>
                        <br>
                        <input type="text" id="fw_report_file_preview" class="widefat" value="<?= esc_attr( $file_url ) ?>" readonly dir="auto">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="fw_report_users">انتخاب کاربران</label>
                    </th>
                    <td>
                        <select name="fw_report_users[]" id="fw_report_users" class="widefat select2-ajax-search-users" multiple="multiple">
							<?php

							if ( ! empty( $users ) ) {
								foreach ( $users as $user_id ) {

									$user = get_user_by( 'ID', $user_id );

									if ( is_wp_error( $user ) || empty( $user ) || ! $user->exists() ) {
										continue;
									}

									echo sprintf( '<option value="%1$s" %4$s>#%1$s %2$s (%3$s)</option>', $user->ID, $user->display_name, $user->user_email, selected( true, true, false ) );

								}
							}
							?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
		<?php
	}

}

MetaBox::init();