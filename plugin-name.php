<?php
/**
 * Plugin Name: Plugin Name
 * Description:
 * Version: NEXT
 * Author: YourCompanyName
 * Author URI: http://example.com
 * Text Domain: plugin-name
 * Network: False
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since     NEXT
 * @package   YourCompanyName\YourPluginName
 */

// Our namespace.
namespace YourCompanyName\YourPluginName;

// Require the App class.
require_once 'includes/class-app.php';

// Create a global variable for the app.
$app = null;

/**
 * Create/Get the App.
 *
 * @author Your Name
 * @since  NEXT
 *
 * @return App The App.
 */
function app() {
	global $app;

	if ( null === $app ) {

		// Create the app and go!
		$app = new App( __FILE__ );

		// Attach our other classes.
		$app->attach();

		// Run any hooks.
		$app->hooks();
	}

	// Load language files.
	load_plugin_textdomain( 'plugin-name', false, basename( dirname( __FILE__ ) ) . '/languages' );

	return $app;
}

// Wait until WordPress is ready, then go!
add_action( 'plugins_loaded', 'YourCompanyName\YourPluginName\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
