<?php
/**
 * Application.
 *
 * @since __NEXT__
 * @package  __YourCompanyName__\__YourPluginName__
 */

namespace __YourCompanyName__\__YourPluginName__;

use Exception;

/**
 * Application Loader.
 *
 * Everything starts here. If you create a new class,
 * attach it to this class using attach() below.
 *
 * @since __NEXT__
 */
class App {

	/**
	 * Plugin basename.
	 *
	 * @author __YourName__
	 * @var    string
	 * @since  __NEXT__
	 */
	public $basename = '';

	/**
	 * URL of plugin directory.
	 *
	 * @author __YourName__
	 * @var    string
	 * @since  __NEXT__
	 */
	public $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @author __YourName__
	 * @var    string
	 * @since  __NEXT__
	 */
	public $path = '';

	/**
	 * Is WP_DEBUG set?
	 *
	 * @since  __NEXT__
	 * @author __YourName__
	 *
	 * @var boolean
	 */
	public $wp_debug = false;

	/**
	 * The plugin file.
	 *
	 * @since  __NEXT__
	 * @author __YourName__
	 *
	 * @var string
	 */
	public $plugin_file = '';

	/**
	 * The plugin headers.
	 *
	 * @since  __NEXT__
	 * @author __YourName__
	 *
	 * @var string
	 */
	public $plugin_headers = '';

	/**
	 * Construct.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param string $plugin_file The plugin file, usually __FILE__ of the base plugin.
	 *
	 * @throws Exception If $plugin_file parameter is invalid (prevents plugin from loading).
	 */
	public function __construct( $plugin_file ) {

		// Check input validity.
		if ( empty( $plugin_file ) || ! stream_resolve_include_path( $plugin_file ) ) {

			// Translators: Displays a message if a plugin file is not passed.
			throw new Exception( sprintf( esc_html__( 'Invalid plugin file %1$s supplied to %2$s', '__your-company__-__plugin-name__' ), $plugin_file, __METHOD__ ) );
		}

		// Plugin setup.
		$this->plugin_file = $plugin_file;
		$this->basename    = plugin_basename( $plugin_file );
		$this->url         = plugin_dir_url( $plugin_file );
		$this->path        = plugin_dir_path( $plugin_file );
		$this->wp_debug    = defined( 'WP_DEBUG' ) && WP_DEBUG;

		// Plugin information.
		$this->plugin_headers = get_file_data( $plugin_file, array(
			'Plugin Name' => 'Plugin Name',
			'Description' => 'Description',
			'Version'     => 'Version',
			'Author'      => 'Author',
			'Author URI'  => 'Author URI',
			'Text Domain' => 'Text Domain',
			'Network'     => 'Network',
			'License'     => 'License',
			'License URI' => 'License URI',
		), 'plugin' );

		// Load language files.
		load_plugin_textdomain( '__your-company__-__plugin-name__', false, basename( dirname( $plugin_file ) ) . '/languages' );

		// Loaders.
		$this->auto_loader();
	}

	/**
	 * Register the autoloader.
	 *
	 * @since __NEXT__
	 * @author __YourName__
	 */
	private function auto_loader() {

		// Register our autoloader.
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Require classes.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param string $class_name Fully qualified name of class to try and load.
	 */
	public function autoload( $class_name ) {

		// Autoload files from parts.
		$this->autoload_from_parts( explode( '\\', $class_name ) );
	}

	/**
	 * Autoload files from self::autoload() parts.
	 *
	 * Note, if you pass any class in here it will look for it in:
	 *
	 * - /app/
	 * - /components/
	 * - /services/
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  array $parts  The parts from self::autoload().
	 * @return void          Early bail once we load the thing.
	 */
	private function autoload_from_parts( $parts ) {

		// app/.
		if ( $this->is_our_file( $this->autoload_app_file( $parts ) ) ) {
			require_once $this->autoload_app_file( $parts );
			return;
		}

		if ( $this->is_our_file( $this->autoload_component_file( $parts ) ) ) {
			require_once $this->autoload_component_file( $parts );
			return;
		}

		// service/.
		if ( $this->is_our_file( $this->autoload_service_file( $parts ) ) ) {
			require_once $this->autoload_service_file( $parts );
			return;
		}

		// Try and find a file in all the directories (recursive), maybe you're using some new file that you aren't even attaching to App.
		if ( $this->is_our_file( $this->autoload_recursive_file( $parts ) ) ) {
			require_once $this->autoload_recursive_file( $parts );
			return;
		}
	}

	/**
	 * Is a file in our plugin?
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  string $file The file.
	 * @return boolean      True if it is and exists.
	 */
	public function is_our_file( $file ) {
		if ( ! is_string( $file ) ) {
			return false;
		}

		return stristr( $file, dirname( $this->plugin_file ) ) && stream_resolve_include_path( $file );
	}

	/**
	 * Autoload a service e.g. service/class-service.php.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  array $parts The parts from self::autoload().
	 * @return string       The path to that service class file.
	 */
	public function autoload_recursive_file( $parts ) {
		$class = end( $parts );

		// Where would it be?
		$file  = $this->autoload_class_file( $parts );
		$class = strtolower( str_replace( '_', '-', $class ) );

		$dirs = [

			// Search these directories in the structure.
			$this->autoload_dir( 'app' ),
			$this->autoload_dir( 'components' ),
			$this->autoload_dir( 'services' ),
		];

		foreach ( $dirs as $dir ) {

			$recursive_dir = new \RecursiveDirectoryIterator( $dir );

			foreach ( new \RecursiveIteratorIterator( $recursive_dir ) as $recursive_file => $file_obj ) {
				if ( ! stristr( $recursive_file, '.php' ) ) {
					continue;
				}

				if ( basename( $recursive_file ) !== $file ) {
					continue;
				}

				return $recursive_file;
			}
		}
	}

	/**
	 * Autoload a service e.g. service/class-service.php.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  array $parts The parts from self::autoload().
	 * @return string       The path to that service class file.
	 */
	public function autoload_service_file( $parts ) {
		$dirs = [
			'services',
			'features', // This is here for backwards compatibility.
		];

		$class = end( $parts );

		foreach ( $dirs as $dir ) {

			// Where would it be?
			$file = $this->autoload_class_file( $parts );
			$dir  = $this->autoload_dir( trailingslashit( $dir ) . strtolower( str_replace( '_', '-', $class ) ) );
			$path = "{$dir}{$file}";

			if ( ! file_exists( $path ) ) {
				continue; // Try again in another directory.
			}

			// Pass back that path.
			return $path;
		}
	}

	/**
	 * Get a file for including from app/.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  array $parts The parts from self::autoload().
	 * @return string       The path to that file.
	 */
	private function autoload_app_file( $parts ) {
		return $this->autoload_dir( 'app' ) . $this->autoload_class_file( $parts );
	}

	/**
	 * Get a file for including from components/.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  array $parts The parts from self::autoload().
	 * @return string       The path to that file.
	 */
	private function autoload_component_file( $parts ) {
		$class = end( $parts );

		// Where would it be?
		$file = $this->autoload_class_file( $parts );
		$dir  = $this->autoload_dir( 'components/' . strtolower( str_replace( '_', '-', $class ) ) );

		// Pass back that path.
		return "{$dir}{$file}";
	}

	/**
	 * Get a directory for autoload.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  string $dir What dir, e.g. app.
	 * @return string      The path to that directory.
	 */
	private function autoload_dir( $dir = '' ) {
		return trailingslashit( $this->path ) . trailingslashit( $dir );
	}

	/**
	 * Generate a class filename to autoload.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  array $parts  The parts from self::autoload().
	 * @return string        The class filename.
	 */
	private function autoload_class_file( $parts ) {
		return 'class-' . strtolower( str_replace( '_', '-', end( $parts ) ) ) . '.php';
	}

	/**
	 * Get the plugin version.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @return string The version of this plugin.
	 */
	public function version() {
		return $this->header( 'Version' );
	}

	/**
	 * Get a header.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  string $header The header you want, e.g. Version, Author, etc.
	 * @return string         The value of the header.
	 */
	public function header( $header ) {
		return isset( $this->plugin_headers[ $header ] )
			? trim( (string) $this->plugin_headers[ $header ] )
			: '';
	}

	/**
	 * Attach items to our app.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	public function attach() {
		$this->attach_services();
	}

	/**
	 * Load and attach app services to the app class.
	 *
	 * Make your classes/element small and do only one thing. If you
	 * need to pass $this to it so you can access other classes
	 * functionality.
	 *
	 * When you add something that gets attached
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	public function attach_services() {

		// An example service so you can see how things work, below cli command should remove this.
		$this->example_service = new Service\Example_Service();

		// Adds wp kickstart for replacements to make this framework into your own plugin.
		$this->replace_cli = new Service\Replace_CLI();
	}

	/**
	 * Fire hooks!
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	public function hooks() {
		$this->auto_call_hooks(); // If you want to run your own hook methods, just strip this.
		// $this->attached_thing->hooks(); // You could do it this way if you want.
	}

	/**
	 * Autoload hooks method.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	private function auto_call_hooks() {
		$this->autocall( 'hooks' );
	}

	/**
	 * Run the app.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	public function run() {
		$this->auto_call_run();
		// $this->attached_thing->run(); // You could do them manually this way if you want.
	}

	/**
	 * Automatically call run methods.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	private function auto_call_run() {
		$this->autocall( 'run' );
	}

	/**
	 * Call a property on attached objects.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  string $call The call.
	 */
	private function autocall( $call ) {
		foreach ( get_object_vars( $this ) as $prop ) {
			if ( is_object( $prop ) ) {
				if ( method_exists( $prop, $call ) ) {
					$prop->$call();
				}
			}
		}
	}

	/**
	 * This plugin's url.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 *
	 * @param  string $path (Optional) appended path.
	 * @return string       URL and path.
	 */
	public function url( $path = '' ) {
		return is_string( $path ) && ! empty( $path ) ?
			trailingslashit( $this->url ) . $path :
			trailingslashit( $this->url );
	}

	/**
	 * Re-attribute user content to site author.
	 *
	 * @author __YourName__
	 * @since  __NEXT__
	 */
	public function deactivate_plugin() {
	}
}
