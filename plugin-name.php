<?php
/**
 * Plugin Name: __PluginName__
 * Description: Description of your plugin goes here.
 * Version:     __NEXT__
 * Author:      __YourCompanyName__
 * Author URI:  http://example.com
 * Text Domain: __plugin-name__
 * Network:     False
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since       __NEXT__
 * @package     __YourCompanyName__\__YourPluginName__
 */

// Our namespace.
namespace __YourCompanyName__\__YourPluginName__;

// Require the App class.
require_once 'includes/class-app.php';

/**
 * Create/Get the App.
 *
 * @author __YourName__
 * @since  __NEXT__
 *
 * @return App The App.
 */
function app() {
	static $app = null;

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
add_action( 'plugins_loaded', '__YourCompanyName__\__YourPluginName__\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
