<?php
/**
 * Example Feature.
 *
 * @since __NEXT__
 * @package  __YourCompanyName__\__YourPluginName__
 */

namespace __YourCompanyName__\__YourPluginName__;

/**
 * Example Feature.
 *
 * @author __YourName__
 * @since __NEXT__
 */
class Example_Feature {

	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer', array( $this, 'footer' ) );
		add_action( 'wp_footer', array( $this, 'footer' ) );
	}

	/**
	 * Footer.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  __NEXT__
	 */
	public function footer() {
		?>
		<span class="example-feature">Example Feature HTML Loaded</span>
		<?php
	}

	/**
	 * Load styles and scripts.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  __NEXT__
	 */
	public function scripts() {
		wp_enqueue_script( 'example-feature', plugins_url( 'features/example-feature/example-feature.js', app()->plugin_file ), array(), app()->version(), true );
		wp_enqueue_style( 'example-feature', plugins_url( 'features/example-feature/example-feature.css', app()->plugin_file ), array(), app()->version() );
	}
}
