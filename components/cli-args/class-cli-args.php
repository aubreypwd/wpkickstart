<?php
/**
 * CLI Arguments.
 *
 * @since 2.0.0
 * @package  WebDevStudios\MigrateSubsite
 */

namespace WebDevStudios\CLI_Args;

/**
 * CLI Arguments.
 *
 * @author Aubrey Portwood <aubreypwd@icloud.com>
 * @since 2.0.0
 */
class CLI_Args {

	/**
	 * The merged CLI args.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $cli_args = [];

	/**
	 * Set the arguments.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @param  array $args       Arguments from CLI class.
	 * @param  array $assoc_args Arguments from CLI class.
	 *
	 * @return array The arguments we saved.
	 */
	public function set_args( array $args = [], array $assoc_args = [] ) : array {
		return $this->cli_args = $this->merge_args( $args, $assoc_args );
	}

	/**
	 * Merge $args and $assoc_args so we have one format to work with.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @param  array $args       Args e.g. array( "sleep' ).
	 * @param  array $assoc_args Assoc args e.g. array( 'thing' => 1, 'thing2' => 'value' ).
	 * @return array             $args reformatted to work like $assoc args.
	 */
	public function merge_args( array $args = [], array $assoc_args = [] ) : array {
		return array_merge( $assoc_args, array_map( function() {

			// E.g. if I do wp migrate sleep we will get sleep set to true like in $assoc_args.
			return true;

		}, array_flip( $args ) ) );
	}

	/**
	 * Is an argument set?
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $arg The argument.
	 * @return bool        True if it is, false if not.
	 */
	public function arg_set( string $arg ) : bool {
		return isset( $this->cli_args[ $arg ] ) ? true : false;
	}

	/**
	 * Get an argument value.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @param  string $arg The argument.
	 * @return mixed       The value of that argument.
	 *
	 * @throws \Exception If you try to get an argument before you run `self::set_args()`.
	 * @throws \Exception If you call this w/out WP CLI.
	 */
	public function get_arg( string $arg ) {
		if ( empty( $this->cli_args ) ) {
			throw new \Exception( 'self::set_args() has not been run yet.' );
		}

		if ( ! class_exists( '\WP_CLI' ) ) {
			throw new Exception( 'WP CLI not present.' );
		}

		if ( ! isset( $this->cli_args[ $arg ] ) ) {
			\WP_CLI::error( "--{$arg} was not set, please run `wp help migrate subsite`." );
		}

		return $this->cli_args[ $arg ];
	}
}
