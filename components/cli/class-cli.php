<?php
/**
 * CLI.
 *
 * @since 2.0.0
 * @package  WebDevStudios\CLI
 */

namespace WebDevStudios\CLI;

/**
 * CLI.
 *
 * Base CLI sharable commands.
 *
 * @since 2.0.0
 */
class CLI {

	/**
	 * Construct.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 */
	public function __construct() {
		$this->ensure_cli_installed();
	}

	/**
	 * Ensure CLI is installed.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @throws \Exception If WP_CLI is not installed.
	 */
	private function ensure_cli_installed() {
		if ( ! defined( 'WP_CLI_VERSION' ) ) {
			throw new \Exception( 'WP CLI does not appear to be installed.' );
		}
	}

	/**
	 * Show success message.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param string $message The message.
	 */
	public function success( $message ) {
		\WP_CLI::success( $message ); // Will continue.
	}

	/**
	 * Show an error.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param string $message The message.
	 */
	public function error( $message ) {
		\WP_CLI::error( $message ); // Will die.
	}

	/**
	 * Show a warning.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param string $message The message.
	 */
	public function warn( $message ) {
		\WP_CLI::warning( $message ); // Will continue.
	}

	/**
	 * Log a message.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param string $message The message.
	 */
	public function log( $message ) {
		\WP_CLI::log( $message ); // Will continue.
	}
}
