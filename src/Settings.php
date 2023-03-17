<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

class Settings {

	public static $options = null;


	public static function init() {

		add_action( 'admin_menu', [ __CLASS__, 'register_admin_menu' ] );

	}


	public static function get_option_id( $key = null ) {

		return Helper::suffix( 'settings' ) . $key;
	}


	public static function get( $key = null, $default = null ) {

		if ( is_null( self::$options ) ) {
			self::$options = get_option( self::get_option_id(), [] );
		}

		if ( empty( self::$options ) ) {
			self::$options = [];
		}

		if ( empty( $key ) ) {
			return self::$options;
		}

		return self::$options[ $key ] ?? $default;
	}


	/**
	 * Get Allow Roles
	 */
	public static function get_roles() {

		return (array) self::get( 'roles', [ get_option( 'default_role' ) ] );
	}


	/**
	 * Register Admin Menu
	 */
	public static function register_admin_menu() {

		add_submenu_page(
			$parent_slug = 'edit.php?post_type=' . PostType::$POST_TYPE,
			$page_title = 'تنظیمات',
			$menu_title = $page_title,
			$capability = 'manage_options',
			$menu_slug = self::get_option_id(),
			$function = [ __CLASS__, 'display_settings' ],
			$position = null
		);
	}


	/**
	 * Display Settings
	 */
	public static function display_settings() {

		global $title;

		// Settings JS
		$js_handle = Helper::suffix( 'settings' );
		Helper::wp_enqueue_script( $js_handle, 'assets/settings.js', [ 'jquery', 'wp-util' ] );

		Ajax::js_vars( $js_handle );

		?>
        <div class="wrap">
            <h1><?= $title ?></h1>

            <form method="post" class="fw-settings">

                <table class="form-table">

                    <tbody>

                        <tr>
                            <th>
                                انتقال این نقش ها به صفحه گزارشات بعد از لاگین
                            </th>
                            <td>
                                <fieldset class="about__section has-4-columns has-gutters">
                                    <input type="hidden" name="<?= self::get_option_id( '[roles]' ) ?>" value="">
									<?php foreach ( User::get_all_roles() as $role_id => $role_name ) : ?>
                                        <label>
                                            <input
                                                    type="checkbox"
                                                    name="<?= self::get_option_id( '[roles][]' ) ?>"
                                                    value="<?= esc_attr( $role_id ) ?>"
												<?php checked( true, in_array( $role_id, self::get_roles(), true ) ) ?>
                                            >
											<?= $role_name ?>
                                        </label>
									<?php endforeach; ?>
                                </fieldset>
                            </td>

                        </tr>

                    </tbody>

                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیره تنظیمات">
                    <span class="spinner" style="float: none"></span>
                </p>
            </form>
        </div>
		<?php
	}

}

Settings::init();