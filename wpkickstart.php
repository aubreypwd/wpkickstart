<?php
/**
 * Plugin Name: wpkickstart
 * Description: A great way to kickstart a new WordPress plugin or project. Just activate and run <code>wp kickstart</code> to get started.
 * Version:     2.0.0
 * Author:      Aubrey Portwood
 * Author URI:  http://github.com/aubreypwd/wpkickstart
 * Text Domain: __your-company__-__plugin-name__
 * Network:     False
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since       __NEXT__
 * @package     __YourCompanyName__\__YourPluginName__
 *
 * Built with https://github.com/aubreypwd/wpkickstart
 */

// Our namespace.
namespace __YourCompanyName__\__YourPluginName__;

// Require the App class.
require_once 'app/class-app.php';

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

		// Run the app.
		$app->run();
	}

	return $app;
}

// Wait until WordPress is ready, then go!
add_action( 'plugins_loaded', '__YourCompanyName__\__YourPluginName__\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
