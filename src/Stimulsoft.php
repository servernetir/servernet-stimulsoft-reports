<?php

namespace ServerNet\Plugins\Stimulsoft_Reports;

class Stimulsoft {

	public static function init() {

		add_action( 'template_redirect', function () {

			if ( ! self::is_valid() ) {
				return;
			}

			if ( ! is_user_logged_in() ) {
				wp_redirect( wp_login_url( site_url( add_query_arg( [] ) ), true ) );
				exit();
			}

			// Check user has access to this report
			if ( ! User::has_access( get_the_ID() ) ) {
				wp_die( 'شما اجازه مشاهده این گزارش را ندارید.', '', [
					'back_link' => true,
					'code'      => 403,
				] );
			}

			add_action( 'wp_body_open', [ __CLASS__, 'ob_start' ], - 9999 );
			add_action( 'wp_footer', [ __CLASS__, 'display' ], 1 );

			self::register_css_js();

		}, 999 );

	}


	public static function is_valid() {

		if ( ! Report::is_report() ) {
			return false;
		}

		if ( empty( get_the_ID() ) ) {
			return false;
		}

		return true;
	}


	public static function register_css_js() {

		// Clean All Scripts & Styles
		remove_all_actions( 'wp_enqueue_scripts' );
		remove_all_actions( 'wp_print_scripts' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_footer_scripts' );

		add_action( 'wp_footer', function () {

			echo '<script type="text/javascript">if(typeof wpOnload===\'function\')wpOnload();</script>';
		}, 999 );

		// Enable Admin bar
		AdminBar::enable();

		// Register StimulSoft Scripts
		Helper::wp_enqueue_style( 'stimulsoft.viewer', 'stimulsoft/css/stimulsoft.viewer.office2013.whiteblue.css' );
		Helper::wp_enqueue_script( 'stimulsoft.reports', 'stimulsoft/scripts/stimulsoft.reports.js' );
		Helper::wp_enqueue_script( 'stimulsoft.dashboards', 'stimulsoft/scripts/stimulsoft.dashboards.js' );
		Helper::wp_enqueue_script( 'stimulsoft.viewer', 'stimulsoft/scripts/stimulsoft.viewer.js' );

		ob_start();

		// Init StimulSoft
		require_once Helper::path( 'stimulsoft/stimulsoft/helper.php' );

		\StiHelper::init( Helper::url( 'stimulsoft/handler.php' ), 30 );

		?>
        <script>

            Stimulsoft.Base.StiLicense.loadFromFile('<?= Helper::url( 'stimulsoft/license.key' ) ?>')

            // Create and set options.
            // More options can be found in the documentation at the link:
            // https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_settings.htm
            var options = new Stimulsoft.Viewer.StiViewerOptions()
            options.toolbar.showSendEmailButton = false
            options.toolbar.showAboutButton = false
            options.toolbar.showFullScreenButton = false
            options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Simple
            options.appearance.fullScreenMode = true
            options.appearance.scrollbarsMode = true
            // options.height = "600px" // Height for non-fullscreen mode
            // options.height = "600px" // Height for non-fullscreen mode

            // Create Viewer component.
            // A description of the parameters can be found in the documentation at the link:
            // https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_showing_reports.htm
            var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false)

            // Optional Viewer events for fine tuning. You can uncomment and change any event or all of them, if necessary.
            // In this case, the built-in handler will be overridden by the selected event.
            // You can read and, if necessary, change the parameters in the args before server-side handler.

            // All events and their details can be found in the documentation at the link:
            // https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_viewer_viewer_events.htm

            // Process report variables before rendering.
            viewer.onPrepareVariables = function (args, callback) {

                // Call the server-side handler
                Stimulsoft.Helper.process(args, callback)
            }

            // Process SQL data sources. It can be used if it is necessary to correct the parameters of the data request.
            viewer.onBeginProcessData = function (args, callback) {

                // Call the server-side handler
                Stimulsoft.Helper.process(args, callback)
            }

            // Manage export settings and, if necessary, transfer them to the server and manage there
            viewer.onBeginExportReport = function (args, callback) {

                // Call the server-side handler
                Stimulsoft.Helper.process(args, callback)

                // Manage export settings
                // args.fileName = "MyReportName";
            }

            // Process exported report file on the server side
            viewer.onEndExportReport = function (args) {

                // Prevent built-in handler (save the exported report as a file)
                // args.preventDefault = true

                // Call the server-side handler
                // Stimulsoft.Helper.process(args)
            }

            // Send exported report to Email
            viewer.onEmailReport = function (args) {

                // Call the server-side handler
                Stimulsoft.Helper.process(args)
            }

            // Create a report and load a template from an MRT file:
            var report = new Stimulsoft.Report.StiReport()
            report.loadFile("<?= Report::get_mrt_file_url( get_the_ID() ) ?>")

            // Assigning a report to the Viewer:
            viewer.report = report

            // After loading the HTML page, display the visual part of the Viewer in the specified container.
            addLoadEvent(function () {
                viewer.renderHtml("viewerContent")
                setViewerOffset()
            })

            function addLoadEvent(func) {
                if (typeof wpOnload !== 'function') {
                    wpOnload = func
                } else {
                    var oldonload = wpOnload
                    wpOnload = function () {
                        oldonload()
                        func()
                    }
                }
            }

            function setViewerOffset() {
                var adminbar = document.getElementById('wpadminbar'),
                    viewer = document.getElementById('StiViewer')

                if (!adminbar || !viewer) {
                    return
                }

                viewer.style.top = adminbar.offsetHeight + 'px'

            }

            window.addEventListener('load', setViewerOffset)
            window.addEventListener('resize', setViewerOffset)
        </script>
		<?php

		$inline_scripts = ob_get_clean();
		$inline_scripts = trim( preg_replace( '#<script\b[^>]*>([\s\S]*?)<\/script>#im', '$1', $inline_scripts ) );

		wp_add_inline_script( 'stimulsoft.viewer', $inline_scripts );

	}


	/**
	 * Display Stimulsoft Report
	 *
	 * @return void
	 */

	public static function ob_start() {

		ob_start();
	}


	public static function display() {

		// Delete body content
		ob_get_clean();

		// Report Wrapper
		echo '<div id="viewerContent" dir="ltr"></div>';

		// Render Admin bar
		AdminBar::render();

	}

}

Stimulsoft::init();