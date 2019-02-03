<?php
/**
 * Plugin Name: wpkickstart
 * Description: A great way to kickstart a new WordPress plugin. Just activate and run <code>wp kickstart</code> to get started.
 * Version:     2.0.0
 * Author:      Aubrey Portwood
 * Author URI:  http://github.com/aubreypwd/wpkickstart
 * Text Domain: company-slug-project-slug
 * Network:     False
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @since       1.0.0
 * @package     aubreypwd\wpkickstart
 *
 * Built with:  wpkickstart
 */

// Our namespace.
namespace aubreypwd\wpkickstart;

// Require the App class.
require_once 'app/class-app.php';

/**
 * Create/Get the App.
 *
 * @author Aubrey Portwood <code@aubreypwd.com>
 * @since  1.0.0
 *
 * @author Aubrey Portwood <aubrey@webdevstudios.com>
 * @since  1.2.0           Fixed issue where this was in global space conflicting with other plugins built off same framework.
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
add_action( 'plugins_loaded', 'aubreypwd\wpkickstart\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
