<?php
/**
 * PHPUnit bootstrap file
 *
 * @package aubreypwd\wpkickstart
 *
 * @since  1.1.0
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 *
 * @since  1.1.0
 * @author Aubrey Portwood <code@aubreypwd.com>
 */
function _manually_load_plugin() {
	$kickstartfile = dirname( dirname( __FILE__ ) ) . '/wpkickstart.php';
	require dirname( dirname( __FILE__ ) ) . file_exists( $kickstartfile ) ? $kickstartfile : 'company-slug-project-slug';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
