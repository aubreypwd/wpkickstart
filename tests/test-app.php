<?php
/**
 * App Tests.
 *
 * @package #{YourCompanyName}\#{YourPluginName}
 * @since   #{NEXT}
 */

namespace #{YourCompanyName}\#{YourPluginName};

use \WP_UnitTestCase;
use \RecursiveDirectoryIterator;
use \FilesystemIterator;
use \RecursiveCallbackFilterIterator;
use \RecursiveIteratorIterator;

/**
 * App Tests.
 *
 * @since   #{NEXT}
 * @package #{YourCompanyName}\#{YourPluginName}
 */
class Test_App extends WP_UnitTestCase {

	/**
	 * Test if App class exists.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_app_class_exists() {
		$this->assertTrue( class_exists( '#{YourCompanyName}\#{YourPluginName}\App' ), '#{YourCompanyName}\#{YourPluginName}\App class should always exist so we can create it.' );
	}

	/**
	 * Test that app() calls the App instance.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_app_function() {
		$this->assertTrue( is_a( app(), '#{YourCompanyName}\#{YourPluginName}\App' ), 'app() should always be an instance of #{YourCompanyName}\#{YourPluginName}\App.' );
	}

	/**
	 * Test that the version method is set to something proper.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_version() {
		$this->method_exists_is_not_empty_and_a_string( 'version' );
	}

	/**
	 * Test that the url method is set to something proper.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_url() {
		$this->method_exists_is_not_empty_and_a_string( 'url' );
		$this->assertTrue( (boolean) filter_var( app()->url(), FILTER_VALIDATE_URL ), 'App::url() needs to return a valid URL.' );
	}

	/**
	 * Test for a semantic version.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_semver() {
		// Un-comment to enable forced semver.
		// $this->assertTrue( version_compare( app()->version(), '0.0.0', '>=' ), 'Plugin version should always be semantic: 0.0.0 or 1.0 or 1.1.1, etc. Note, #{NEXT} will need to be replaced with a semantic value.' ); // @codingStandardsIgnoreLine
	}

	/**
	 * Test that the app's properties are properly set.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_app_properties() {

		// ->plugin_file.
		$this->assertTrue( property_exists( app(), 'plugin_file' ), 'App::plugin_file property is used in other places in the plugin and must exist' );
		$this->assertNotEmpty( app()->plugin_file, 'App::plugin_file should always be set to the #{plugin-name}.php file.' );
		$this->assertFileExists( app()->plugin_file, 'App::plugin_file should always be set to a file that exists.' );

		// ->url.
		$this->assertTrue( property_exists( app(), 'url' ), 'App::url property is used in other places in the plugin and must exist' );
		$this->assertNotEmpty( app()->url, 'App::url should always be set to a valid string.' );
		$this->assertTrue( (boolean) filter_var( app()->url, FILTER_VALIDATE_URL ), 'App::url should always be a valid URL.' );

		// ->path.
		$this->assertTrue( property_exists( app(), 'path' ), 'App::path property is used in other places in the plugin and must exist' );
		$this->assertNotEmpty( app()->path, 'App::path should always be set to the #{plugin-name}.php file.' );
		if ( method_exists( $this, 'assertDirectoryExists' ) ) {

			// Path must be a directory that exists.
			$this->assertDirectoryExists( app()->path, 'App::path should always be set to a valid directory that exists.' );
		}

		// ->plugin_headers.
		$this->assertTrue( property_exists( app(), 'plugin_headers' ), "App::plugin_headers should be set to an array of header information from the plugin file, but doesn't even exist." );
		$this->assertNotEmpty( app()->plugin_headers, 'App::plugin_headers should be set to an array of header information from the plugin file.' );
		$this->assertTrue( is_array( app()->plugin_headers ), 'App::plugin_headers should be set to an array of header information from the plugin file.' );

		// ->wp_debug.
		$this->assertTrue( property_exists( app(), 'wp_debug' ), "App::wp_debug should be set to a boolean, but doesn't even exist." );
		$this->assertTrue( is_bool( app()->wp_debug ), 'App::wp_debug should be set to a boolean.' );
	}

	/**
	 * Test that autoloading functions exist.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_autoloader() {

		// If the auto_loader method exists, assume it's looking for autoload.
		if ( method_exists( app(), 'auto_loader' ) ) {
			$this->assertTrue( method_exists( app(), 'autoload' ), 'App::autoload() required to autoload classes.' );
		}
	}

	/**
	 * Test that all folders are protected against directory browsing.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function test_all_folders_are_protected() {

		// Recursive directory iterator for current directory, ignoring dots.
		$it = new RecursiveDirectoryIterator( app()->path, FilesystemIterator::SKIP_DOTS );

		// Exclude some folders from being examined.
		$it = new RecursiveCallbackFilterIterator( $it, array( $this, 'exclude_folders_from_index_dot_php_protection' ) );

		// Get new set of iterations.
		$it = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::SELF_FIRST );

		// And then just loop :)...
		foreach ( $it as $file ) {

			// We just want the directory for the file.
			$dir = dirname( $file->getRealPath() );

			// Find out if the directory has a index.php file to protect it.
			$this->assertTrue( file_exists( "{$dir}/index.php" ), "{$dir} needs to have a index.php file in it to protect it from directory browsing." );
		}
	}

	/**
	 * Ensure that a method exists, is not empty when it returns, and returns a string.
	 *
	 * @param string $function_name The function name.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	private function method_exists_is_not_empty_and_a_string( $function_name ) {
		$this->assertTrue( method_exists( app(), $function_name ), "App::{$function_name} method must exist, it could be used throughout the plugin." );
		$this->assertNotEmpty( app()->$function_name(), "Make sure {$function_name} returns a non-empty value." );
		$this->assertTrue( is_string( app()->$function_name() ), "App::{$function_name} should always return a string from the Plugin file's header, something might have changed in the method." );
	}

	/**
	 * Files to ignore for testing all folders are protected.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 *
	 * @param  string $file     The file.
	 * @param  string $key      The key.
	 * @param  string $iterator The iterator.
	 * @return boolean          True if it's okay to be included.
	 */
	public function exclude_folders_from_index_dot_php_protection( $file, $key, $iterator ) {

		// If you add a folder you don't want tests to look for index.php files in, add them to the array here.
		return ! in_array( $file->getFilename(), array(
			'.git',
			'node_modules',
		), true );
	}
}
