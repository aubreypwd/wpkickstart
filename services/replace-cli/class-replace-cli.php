<?php

/**
 * Command line replacements.
 *
 * You'll want to modify, delete, or keep this file around
 * for easy duplication.
 *
 * @since 2.0.0
 * @package  __YourCompanyName__\__YourPluginName__
 */

namespace __YourCompanyName__\__YourPluginName__\Service;

use function \__YourCompanyName__\__YourPluginName__\app;

/**
 * Replaces stuff to convert this to your plugin.
 *
 * @since  2.0.0
 */
class Replace_CLI {

	/**
	 * Line removals.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $line_removals = [
		'app/class-app.php' => [ 394, 395, 396, 397, 398, 399 ],
	];

	/**
	 * File removals.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $file_removals = [];

	/**
	 * CLI arguments.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since 2.0.0
	 *
	 * @var \WebDevStudios\CLI_Args\CLI_Args
	 */
	private $cli_args;

	/**
	 * Only work on these extensions.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $extensions = [
		'.php',
		'.md',
		'.js',
	];

	/**
	 * Directories to ignore modifications.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $ignore_dirs = [
		'vendor',
	];

	/**
	 * WP File System.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @var \WP_Filesystem_Direct
	 */
	private $fs;

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @return void Early bail if not CLI.
	 */
	public function hooks() {
		if ( ! class_exists( '\WP_CLI' ) ) {
			return;
		}
	}

	/**
	 * Construct.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 */
	public function __construct() {
		if ( class_exists( '\WP_CLI' ) ) {
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';

			$this->fs = new \WP_Filesystem_Direct( true );

			$this->cli_args = new \WebDevStudios\CLI_Args\CLI_Args();
		}
	}

	/**
	 * Run.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @return void Early bail if not CLI.
	 */
	public function run() {
		if ( ! class_exists( '\WP_CLI' ) ) {
			return;
		}

		\WP_CLI::add_command( 'kickstart', [ $this, 'kickstart' ], [
			'shortdesc' => __( 'Will help you convert the installed wpkickstart plugin into a new plugin and perform all of the search/replacements.', 'wds-migrate-subsite' ),
			'synopsis'  => [
				[
					'type'        => 'assoc',
					'name'        => 'since',
					'optional'    => true,
					'description' => __( 'What @since will be set to, defaults to `1.0.0`.', 'wpkickstart' ),
					'default'     => '1.0.0',
				],
			],
		] );
	}

	/**
	 * WP CLI command.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  array $args       Arguments.
	 * @param  array $assoc_args Arguments.
	 */
	public function kickstart( array $args, array $assoc_args ) {
		$this->cli_args->set_args( $args, $assoc_args ); // Ensure we have an easy way to get arguments.
		$this->remove_lines();
	}

	/**
	 * Remove specific lines from files.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @throws \Exception If we can't remove a line you've specified.
	 */
	private function remove_lines() {
		$plugin_dir = dirname( app()->plugin_file );

		$recursive_dir = new \RecursiveDirectoryIterator( $plugin_dir );

		foreach ( new \RecursiveIteratorIterator( $recursive_dir ) as $file => $file_obj ) {
			if ( ! is_string( $file ) ) {
				continue;
			}

			if ( is_dir( $file ) ) {
				continue;
			}

			if ( ! app()->is_our_file( $file ) ) {
				continue;
			}

			if ( ! $this->has_valid_extension( $file ) ) {
				continue;
			}

			if ( $this->ignore( $file ) ) {
				continue;
			}

			$relative_file = ltrim( str_replace( $plugin_dir, '', $file ), '/' );

			if ( ! in_array( $relative_file, array_keys( $this->line_removals ), true ) ) {
				continue;
			}

			$lines = $this->line_removals[ $relative_file ];

			$file_content_array = $this->fs->get_contents_array( $file );

			foreach ( $lines as $line ) {
				if ( ! isset( $file_content_array[ $line ] ) ) {
					throw new \Exception( "{$line} is not in {$file}." );
				}

				// Remove that line.
				unset( $file_content_array[ $line - 1 ] );
			}

			// @codingStandardsIgnoreLine: We want this, it's cheap and works with an array.
			file_put_contents( $file, $file_content_array );
		}
	}

	/**
	 * Should we ignore a file?
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return boolean      True if we should, false if not.
	 */
	private function ignore( string $file ) {
		foreach ( $this->ignore_dirs as $ignore_dir ) {
			if ( stristr( $file, trailingslashit( $ignore_dir ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Does a file have a valid extension?
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return boolean      True if it does, false if not.
	 */
	private function has_valid_extension( string $file ) {
		foreach ( $this->extensions as $extension ) {
			if ( stristr( $file, $extension ) ) {
				return true;
			}
		}

		return false;
	}
}
