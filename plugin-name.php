<?php
/**
 * Plugin Name:
 * Plugin URI:
 * Description:
 * Version:
 * Author:
 * Author URI:
 * License:
 * License URI:
 * Text Domain:
 * Network:
 *
 * @since Unknown
 * @package  Company/Package
 */

// Our namespace.
namespace Company\Package;

// Classes outside our namespace.
use Exception;

// Require the App class.
require_once 'includes/class-app.php';

/**
 * Helper function to access the application instance for the Client Plugin.
 *
 * @author Unknown
 * @since Unknown
 *
 * @return App|null App if success, null if exception caught (error will be logged).
 */
function app() {
	static $app;

	if ( ! $app instanceof App ) {

		// Start the app.
		try {
			$app = new App( __FILE__ );
		} catch ( Exception $e ) {

			// Catch any errors and log them if debugging is enabled.
			if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {

				// @codingStandardsIgnoreLine Conditionally debug.
				error_log( $e->getMessage() );
			}

			// Return null so no further action can take place.
			return null;
		}
	}

	return $app;
}
app(); // Initialize the app.

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
