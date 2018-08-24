<?php
/**
 * Application.
 *
 * @since #{NEXT}
 * @package  #{YourCompanyName}\#{YourPluginName}
 */

namespace #{YourCompanyName}\#{YourPluginName};

use Exception;

/**
 * Application Loader.
 *
 * Everything starts here. If you create a new class,
 * attach it to this class using attach() below.
 *
 * @since #{NEXT}
 */
class App {

	/**
	 * Plugin basename.
	 *
	 * @author #{YourName}
	 * @var    string
	 * @since  #{NEXT}
	 */
	public $basename = '';

	/**
	 * URL of plugin directory.
	 *
	 * @author #{YourName}
	 * @var    string
	 * @since  #{NEXT}
	 */
	public $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @author #{YourName}
	 * @var    string
	 * @since  #{NEXT}
	 */
	public $path = '';

	/**
	 * Is WP_DEBUG set?
	 *
	 * @since  #{NEXT}
	 * @author #{YourName}
	 *
	 * @var boolean
	 */
	public $wp_debug = false;

	/**
	 * The plugin file.
	 *
	 * @since  #{NEXT}
	 * @author #{YourName}
	 *
	 * @var string
	 */
	public $plugin_file = '';

	/**
	 * The plugin headers.
	 *
	 * @since  #{NEXT}
	 * @author #{YourName}
	 *
	 * @var string
	 */
	public $plugin_headers = '';

	/**
	 * Construct.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 *
	 * @param string $plugin_file The plugin file, usually __FILE__ of the base plugin.
	 *
	 * @throws Exception If $plugin_file parameter is invalid (prevents plugin from loading).
	 */
	public function __construct( $plugin_file ) {

		// Check input validity.
		if ( empty( $plugin_file ) || ! stream_resolve_include_path( $plugin_file ) ) {

			// Translators: Displays a message if a plugin file is not passed.
			throw new Exception( sprintf( esc_html__( 'Invalid plugin file %1$s supplied to %2$s', '#{plugin-name}' ), $plugin_file, __METHOD__ ) );
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
		load_plugin_textdomain( '#{plugin-name}', false, basename( dirname( $plugin_file ) ) . '/languages' );

		// Loaders.
		$this->auto_loader();
	}

	/**
	 * Register the autoloader.
	 *
	 * @since #{NEXT}
	 * @author #{YourName}
	 */
	private function auto_loader() {

		// Register our autoloader.
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Require classes.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 *
	 * @param string $class_name Fully qualified name of class to try and load.
	 *
	 * @return  void Early exit if we can't load the class.
	 */
	public function autoload( $class_name ) {

		// If our class doesn't have our namespace, don't load it.
		if ( 0 !== strpos( $class_name, '#{YourCompanyName}\\#{YourPluginName}\\' ) ) {
			return;
		}

		$parts = explode( '\\', $class_name );

		// Include our file.
		$includes_dir = trailingslashit( $this->path ) . 'includes/';
		$file         = 'class-' . strtolower( str_replace( '_', '-', end( $parts ) ) ) . '.php';

		if ( stream_resolve_include_path( $includes_dir . $file ) ) {
			require_once $includes_dir . $file;
		}
	}

	/**
	 * Get the plugin version.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 *
	 * @return string The version of this plugin.
	 */
	public function version() {
		return $this->header( 'Version' );
	}

	/**
	 * Get a header.
	 *
	 * @author Aubrey Portwood
	 * @since  #{NEXT}
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
	 * Load and attach app elements to the app class.
	 *
	 * Make your classes/element small and do only one thing. If you
	 * need to pass $this to it so you can access other classes
	 * functionality.
	 *
	 * When you add something that gets attached
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function attach() {
		$this->shared = new Shared();
		// $this->attached_thing = new Attached_Thing();
	}

	/**
	 * Fire hooks!
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function hooks() {
		// $this->attached_thing->hooks();
	}

	/**
	 * This plugin's url.
	 *
	 * @author #{YourName}
	 * @since  #{NEXT}
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
	 * @author #{YourName}
	 * @since  #{NEXT}
	 */
	public function deactivate_plugin() {
	}
}
