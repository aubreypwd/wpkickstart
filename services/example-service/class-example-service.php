<?php
/**
 * Example Feature.
 *
 * You'll want to modify, delete, or keep this file around
 * for easy duplication.
 *
 * @since 2.0.0
 * @package  aubreypwd\wpkickstart
 *
 * This file will get removed when you run wp kickstart (and assets).
 */

// Note the namespace here should follow the namespace of the project.
namespace aubreypwd\wpkickstart\Service;

// Since we're using the \Service namespace to separate from conflicts with components, we need to use app() from the base.
use function \aubreypwd\wpkickstart\app;

/**
 * Example Feature.
 *
 * @author Aubrey Portwood
 * @since 2.0.0
 */
class Example_Service {

	/**
	 * Construct.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 */
	public function __construct() {
		$this->example_component = new \WpKickStart\Components\Example_Component();
	}

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  2.0.0
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer', array( $this, 'content' ) );
		add_action( 'wp_footer', array( $this, 'content' ) );
	}

	/**
	 * Run.
	 *
	 * @author Aubrey Portwood
	 * @since  2.0.0
	 */
	public function run() {
		// Nothing, but here you could do things separate from hooks (automatically called).
	}

	/**
	 * Content.
	 *
	 * Here we're going to call something from a component.
	 *
	 * @author Aubrey Portwood
	 * @since  2.0.0
	 */
	public function content() {
		?>
		<span class="example-service"><?php echo esc_html( $this->example_component->get_example_text() ); ?></span>
		<?php
	}

	/**
	 * Load styles and scripts.
	 *
	 * This service has it's own JS and CSS!
	 *
	 * @author Aubrey Portwood
	 * @since  2.0.0
	 */
	public function scripts() {
		wp_enqueue_script( 'example-service', plugins_url( 'services/example-service/example-service.js', app()->plugin_file ), array(), app()->version(), true );
		wp_enqueue_style( 'example-service', plugins_url( 'services/example-service/example-service.css', app()->plugin_file ), array(), app()->version() );
	}
}
