<?php
/**
 * Plugin Name: Plugin Name
 * Description: Description of your plugin goes here.
 * Version:     NEXT
 * Author:      YourCompanyName
 * Author URI:  http://example.com
 * Text Domain: plugin-name
 * Network:     False
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since       NEXT
 * @package     YourCompanyName\YourPluginName
 */

// Our namespace.
namespace YourCompanyName\YourPluginName;

// Require the App class.
require_once 'includes/class-app.php';

/**
 * Create/Get the App.
 *
 * @author Your Name
 * @since  NEXT
 *
 * @return App The App.
 */
function app() {
	$app = null;

	if ( null === $app ) {

		// Create the app and go!
		$app = new App( __FILE__ );

		// Attach our other classes.
		$app->attach();

		// Run any hooks.
		$app->hooks();
	}

	return $app;
}

// Wait until WordPress is ready, then go!
add_action( 'plugins_loaded', 'YourCompanyName\YourPluginName\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
