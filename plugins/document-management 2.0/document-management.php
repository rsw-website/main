<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              document-management
 * @since             1.0.0
 * @package           Document_Management
 *
 * @wordpress-plugin
 * Plugin Name:       Documents Management 2.0
 * Plugin URI:        document-management
 * Description:       Display list of all uploaded documents in admin end and user end based on user role and document tags.
 * Version:           1.0.0
 * Author:            Mindfire Solutions
 * Author URI:        document-management
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       document-management
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
define( 'DOCUMENT_MANAGEMENT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-document-management-activator.php
 */
function activate_document_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-document-management-activator.php';
	Document_Management_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-document-management-deactivator.php
 */
function deactivate_document_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-document-management-deactivator.php';
	Document_Management_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_document_management' );
register_deactivation_hook( __FILE__, 'deactivate_document_management' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-document-management.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_document_management() {

	$plugin = new Document_Management();
	$plugin->run();

}

run_document_management();
