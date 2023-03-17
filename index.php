<?php
/**
 * Plugin Name: نمایش گزارشات دیتابیس ها
 * Plugin URI:   https://ServerNet.ir/
 * Version:      1.0.0
 * Author:       ServerNet
 * Author URI:   https://ServerNet.ir/
 * Description:         افزونه ای جهت نمایش گزارشات از انواع دیتابیس های مختلف در محیط وردپرس
 * Requires PHP: 5.6
 * Requires at least: 5.0
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

define( 'FW_STIMULSOFT_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'src/Helper.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Report.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Upload.php';
require_once plugin_dir_path( __FILE__ ) . 'src/PostType.php';
require_once plugin_dir_path( __FILE__ ) . 'src/MetaBox.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Settings.php';
require_once plugin_dir_path( __FILE__ ) . 'src/User.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'src/AdminBar.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Stimulsoft.php';
