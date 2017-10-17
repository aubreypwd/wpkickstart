<?php
/**
 * Main Application Instance.
 *
 * @since Unknown
 * @package  Company/Package
 */

namespace Company\Package;
use Exception;

/**
 * Application Loader.
 *
 * Everything starts here. If you create a new class,
 * attach it to this class.
 *
 * @author Unknown
 * @since Unknown
 */
class App {

	/**
	 * Plugin basename.
	 *
	 * @author Unknown
	 * @var    string
	 * @since Unknown
	 */
	protected $basename = '';

	/**
	 * URL of plugin directory.
	 *
	 * @author Unknown
	 * @var    string
	 * @since Unknown
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @author Unknown
	 * @var    string
	 * @since Unknown
	 */
	protected $path = '';

	/**
	 * Construct.
	 *
	 * @author Unknown
	 * @since Unknown
	 *
	 * @param string $plugin_file The plugin file, usually __FILE__ of the base plugin.
	 *
	 * @throws Exception If $plugin_file parameter is invalid (prevents plugin from loading).
	 */
	public function __construct( $plugin_file ) {

		// Check input validity.
		if ( empty( $plugin_file ) || ! stream_resolve_include_path( $plugin_file ) ) {

			// Translators: Displays a message if a plugin file is not passed.
			throw new Exception( sprintf( esc_html__( 'Invalid plugin file %1$s supplied to %2$s', 'company-package' ), $plugin_file, __METHOD__ ) );
		}

		// Plugin setup.
		$this->basename = plugin_basename( $plugin_file );
		$this->url      = plugin_dir_url( $plugin_file );
		$this->path     = plugin_dir_path( $plugin_file );

		// Loaders.
		$this->auto_loader();
		$this->attach();
	}

	/**
	 * Register the autoloader.
	 *
	 * @since Unknown
	 * @author Unknown
	 */
	private function auto_loader() {

		// Register our autoloader.
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Require classes.
	 *
	 * @author Unknown
	 * @since Unknown
	 *
	 * @param string $class_name Fully qualified name of class to try and load.
	 *
	 * @return  void Early exit if we can't load the class.
	 */
	public function autoload( $class_name ) {

		// If our class doesn't have our namespace, don't load it.
		if ( 0 !== strpos( $class_name, 'Company\\Package\\' ) ) {
			return;
		}

		$parts = explode( '\\', $class_name );

		// Include our file.
		$includes_dir = trailingslashit( $this->path ) . 'includes/';
		$file = 'class-' . strtolower( str_replace( '_', '-', end( $parts ) ) ) . '.php';

		if ( stream_resolve_include_path( $includes_dir . $file ) ) {
			require_once $includes_dir . $file;
		}
	}

	/**
	 * Load and attach app elements to the app class.
	 *
	 * Make your classes/element small and do only one thing. If you
	 * need to pass $this to it so you can access other classes
	 * functionality.
	 *
	 * When you add something that gets attached
	 *
	 * @author Unknown
	 * @since Unknown
	 */
	private function attach() {
		$this->shared = new Shared();
	}

	/**
	 * This plugin's url.
	 *
	 * @author Unknown
	 * @since Unknown
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
	 * @author Unknown
	 * @author Unknown
	 *
	 * @since Unknown
	 */
	public function deactivate_plugin() {
	}
}
