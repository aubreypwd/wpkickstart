<?php
/**
 * App Tests.
 *
 * @package YourCompanyName\YourPluginName
 * @since   NEXT
 */

namespace YourCompanyName\YourPluginName;

use \WP_UnitTestCase;

/**
 * App Tests.
 *
 * @since   NEXT
 * @package App
 */
class Test_App extends WP_UnitTestCase {

	/**
	 * Test if App class exists.
	 *
	 * @author Your Name
	 * @since  NEXT
	 */
	public function test_app_class_exists() {
		$this->assertTrue( class_exists( 'YourCompanyName\YourPluginName\App' ), 'YourCompanyName\YourPluginName\App class should always exist so we can create it.' );
	}

	/**
	 * Test that app() calls the App instance.
	 *
	 * @author Your Name
	 * @since  NEXT
	 */
	public function test_app_function() {
		$this->assertTrue( is_a( app(), 'YourCompanyName\YourPluginName\App' ), 'app() should always be an instance of YourCompanyName\YourPluginName\App.' );
	}

	/**
	 * Test that the version is set to something proper.
	 *
	 * @author Your Name
	 * @since  NEXT
	 */
	public function test_version() {
		$this->assertTrue( method_exists( app(), 'version' ), 'App::version() method must exist, it could be used throughout the plugin.' );
		$this->assertNotEmpty( app()->version(), "Please make sure you have Version defined in your plugin's PHP file." );
		$this->assertTrue( is_string( app()->version() ), "App::version() should always return a string from the Plugin file's header, something might have changed in the method." );
	}

	/**
	 * Test for a semantic version.
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 */
	public function test_semver() {
		// Un-comment to enable forced semver.
		// $this->assertTrue( version_compare( app()->version(), '0.0.0', '>=' ), 'Plugin version should always be semantic: 0.0.0 or 1.0 or 1.1.1, etc. Note, NEXT will need to be replaced with a semantic value.' ); // @codingStandardsIgnoreLine
	}

	/**
	 * Test that the app's properties are properly set.
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 */
	public function test_app_properties() {

		// ->plugin_file.
		$this->assertTrue( property_exists( app(), 'plugin_file' ), 'App::plugin_file property is used in other places in the plugin and must exist' );
		$this->assertNotEmpty( app()->plugin_file, 'App::plugin_file should always be set to the plugin-name.php file.' );
		$this->assertFileExists( app()->plugin_file, 'App::plugin_file should always be set to a file that exists.' );

		// ->url.
		$this->assertTrue( property_exists( app(), 'url' ), 'App::url property is used in other places in the plugin and must exist' );
		$this->assertNotEmpty( app()->url, 'App::url should always be set to a valid string.' );
		$this->assertTrue( (boolean) filter_var( app()->url, FILTER_VALIDATE_URL ), 'App::url should always be a valid URL.' );

		// ->path.
		$this->assertTrue( property_exists( app(), 'path' ), 'App::path property is used in other places in the plugin and must exist' );
		$this->assertNotEmpty( app()->path, 'App::path should always be set to the plugin-name.php file.' );
		if ( method_exists( $this, 'assertDirectoryExists' ) ) {

			// Path must be a directory that exists.
			$this->assertDirectoryExists( app()->path, 'App::path should always be set to a valid directory that exists.' );
		}

		// ->plugin_headers
		$this->assertTrue( property_exists( app(), 'plugin_headers' ), 'App::plugin_headers should be set to an array of header information from the plugin file.' );
		$this->assertNotEmpty( app()->plugin_headers, 'App::plugin_headers should be set to an array of header information from the plugin file.' );
		$this->assertTrue( is_array( app()->plugin_headers ), 'App::plugin_headers should be set to an array of header information from the plugin file.' );
	}

	/**
	 * Test that autoloading functions exist.
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 */
	public function test_autoloader() {

		// If the auto_loader method exists, assume it's looking for autoload.
		if ( method_exists( app(), 'auto_loader' ) ) {
			$this->assertTrue( method_exists( app(), 'autoload' ), 'App::autoload() required to autoload classes.' );
		}
	}
}
