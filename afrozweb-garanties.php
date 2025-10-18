<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://linkedin.com/in/saeid-sadigh-zadeh-8861688a
 * @since             1.0.0
 * @package           Afrozweb_Garanties
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Warranty Management
 * Plugin URI:        https://github.com/saeid6780/afrozweb-garanties
 * Description:       Book Info WordPress Plugin
 * Version:           1.0.0
 * Author:            saeid6780
 * Author URI:        https://linkedin.com/in/saeid-sadigh-zadeh-8861688a/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       afrozweb-garanties
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AFROZWEB_GARANTIES_VERSION', '1.0.0' );
define( 'AFROZWEB_GARANTY_SLUG', 'afrozweb-garanties' );
define( 'AFROZWEB_GARANTY_BASE', plugin_dir_path( __FILE__ ) );
define( 'AFROZWEB_GARANTY_TEMPLATE', AFROZWEB_GARANTY_BASE . 'templates/' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-afrozweb_garanties-activator.php
 */
function activate_afrozweb_garanties() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-afrozweb_garanties-activator.php';
	Afrozweb_Garanties_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-afrozweb_garanties-deactivator.php
 */
function deactivate_afrozweb_garanties() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-afrozweb_garanties-deactivator.php';
	Afrozweb_Garanties_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_afrozweb_garanties' );
register_deactivation_hook( __FILE__, 'deactivate_afrozweb_garanties' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-afrozweb_garanties.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_afrozweb_garanties() {

	$plugin = new Afrozweb_Garanties();
	$plugin->run();

}
run_afrozweb_garanties();
