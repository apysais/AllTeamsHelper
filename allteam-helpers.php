<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.alteams.nz
 * @since             1.0.0
 * @package           AllTeams_Helpers
 *
 * @wordpress-plugin
 * Plugin Name:       AllTeams
 * Plugin URI:        http://www.alteams.nz
 * Description:       This is use for helpers.
 * Version:           1.2.0
 * Author:            AllTeams
 * Author URI:        http://www.alteams.nz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       allteam-helpers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * For autoloading classes
 * */
spl_autoload_register('allteam_autoload_class');
function allteam_autoload_class($class_name){
    if ( false !== strpos( $class_name, 'AllT' ) ) {
		$include_classes_dir = realpath( get_template_directory( __FILE__ ) ) . DIRECTORY_SEPARATOR;
		$admin_classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
		$class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
		if( file_exists($include_classes_dir . $class_file) ){
			require_once $include_classes_dir . $class_file;
		}
		if( file_exists($admin_classes_dir . $class_file) ){
			require_once $admin_classes_dir . $class_file;
		}
	}
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-allteam-helpers-activator.php
 */
function activate_allteam_helpers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-allteam-helpers-activator.php';
	Allteam_Helpers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-allteam-helpers-deactivator.php
 */
function deactivate_allteam_helpers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-allteam-helpers-deactivator.php';
	Allteam_Helpers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_allteam_helpers' );
register_deactivation_hook( __FILE__, 'deactivate_allteam_helpers' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-allteam-helpers.php';
if( !function_exists('wp_multisite_nav_menu') ){
	function wp_multisite_nav_menu( $args = array(), $origin_id = 1 ) {

		global $blog_id;
		$origin_id = absint( $origin_id );

		if ( !is_multisite() || $origin_id == $blog_id ) {
			wp_nav_menu( $args );
			return;
		}

		switch_to_blog( $origin_id );
		wp_nav_menu( $args );   
		restore_current_blog();

	}
}
function _dump($array, $exit = false){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	if( $exit )
		exit();
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_allteam_helpers() {

	$plugin = new Allteam_Helpers();
	$plugin->run();
	
	AllT_NavMenuChildrenShortcode::get_instance();
}
//run_allteam_helpers();
add_action('plugins_loaded', 'run_allteam_helpers');
