<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;


class AdminBar {

	public static function init() {

		add_action( 'admin_bar_menu', [ __CLASS__, 'admin_bar' ], 99999999 );
		add_action( 'wp_head', [ __CLASS__, 'admin_bar_css' ], 99999999 );

	}


	/**
	 * Enable Show WP Admin bar
	 *
	 * @return void
	 */
	public static function enable() {

		// force to show admin bar
		remove_all_filters( 'show_admin_bar' );
		add_filter( 'show_admin_bar', '__return_true' );

		_wp_admin_bar_init();

		remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 ); // Back-compat for themes not using `wp_body_open`.
		remove_action( 'in_admin_header', 'wp_admin_bar_render', 0 );

	}


	public static function admin_bar( \WP_Admin_Bar $wp_admin_bar ) {

		if ( is_super_admin() ) {
			return;
		}

		foreach ( $wp_admin_bar->get_nodes() as $node_id => $node ) {

			if ( in_array( 'my-account', [ $node_id, $node->parent ] ) ) {
				continue;
			}

			$wp_admin_bar->remove_node( $node_id );
		}

		/**
		 * Show User Reports List
		 */
		$reports = User::get_reports();

		if ( ! empty( $reports ) ) {

			$icon  = '<span class="ab-icon" aria-hidden="true"></span>';
			$title = '<span class="ab-label" aria-hidden="true"> گزارشات ( ' . number_format_i18n( count( $reports ) ) . ' )</span>';

			$wp_admin_bar->add_node( [
				'id'    => 'fw-user-reports',
				'title' => $icon . $title,
			] );

			foreach ( $reports as $report ) {

				$current = '○ ';

				if ( $report->ID === get_the_ID() ) {
					$current = '● ';
				}

				$wp_admin_bar->add_node( [
					'parent' => 'fw-user-reports',
					'id'     => 'fw-report-' . $report->ID,
					'title'  => $current . $report->post_title,
					'href'   => get_permalink( $report->ID ),
				] );
			}

		}

		/**
		 * User-related, aligned right.
		 */
		add_action( 'fw_admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
		add_action( 'fw_admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
		add_action( 'fw_admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 );

		do_action( 'fw_admin_bar_menu', $wp_admin_bar );

	}


	public static function admin_bar_css() {

		?>
        <style>
            #wpadminbar {
                z-index: 9999999999999 !important;
            }

            #wpadminbar #wp-admin-bar-fw-user-reports .ab-icon:before {
                content: "\f183";
                top:     3px;
            }
        </style>
		<?php

	}


	/**
	 * Render WP Admin bar HTML
	 *
	 * @return void
	 */
	public static function render() {

		wp_admin_bar_render();

	}

}

AdminBar::init();