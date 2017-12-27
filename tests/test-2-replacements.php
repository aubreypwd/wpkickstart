<?php
/**
 * App Replacements.
 *
 * This will help you ensure you didn't leave anything behind
 * from the original framework.
 *
 * See __construct on how to enable these tests.
 *
 * @package YourCompanyName\YourPluginName
 * @since   NEXT
 */

namespace YourCompanyName\YourPluginName;

use \WP_UnitTestCase;

/**
 * App Replacements.
 *
 * @since   NEXT
 * @package YourCompanyName\YourPluginName
 */
class Test_Replacements extends WP_UnitTestCase {

	/**
	 * The Environment variable you have to set to skip to skip these tests.
	 *
	 * To skip these tests you have to set an explicit environment variable
	 * by running the following before you run phpunit.
	 *
	 *     export PHPUNIT_TEST_REPLACEMENTS=skip
	 *
	 * This is mainly used by Aubrey Portwood when maintaining the Tests
	 * so that he doesn't have to replace everything to run successful tests.
	 *
	 * @author Your Name
	 * @since  NEXT
	 *
	 * @var string
	 */
	private $env_name = 'PHPUNIT_TEST_REPLACEMENTS';

	/**
	 * Make sure plugin-name.php got renamed to the right thing.
	 *
	 * @author Your Name
	 * @since  NEXT
	 */
	public function test_plugin_name_rename() {
		if ( 'skip' !== getenv( $this->env_name ) ) {
			global $app;
			$parent = basename( dirname( $app->plugin_file ) );
			$this->assertFalse( file_exists( "{$app->path}plugin-name.php" ), "Please rename {$app->path}plugin-name.php to {$parent}.php" );
		}
	}
}
