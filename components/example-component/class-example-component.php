<?php
/**
 * Example Feature.
 *
 * You'll want to modify, delete, or keep this file around
 * for easy duplication.
 *
 * @since 2.0.0
 * @package  aubreypwd\ExampleComponent
 */

/*
 * Note on namespace.
 *
 * This component actually uses a namespace outside of the plugin
 * and of it's services, so we use it in the example service appropriately.
 *
 * This allows this component to move between wpkickstart frameworks easily
 * and _could_ even be packages!
 */
namespace FakeCompany\Fake;

/**
 * Example component.
 *
 * @author Aubrey Portwood
 * @since 2.0.0
 */
class Example_Component {

	/**
	 * Get some example text.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @return string Example text.
	 */
	public function get_example_text() {
		return 'Example Feature HTML Loaded';
	}
}
