<?php
/**
 * Build a zip for easy installation.
 *
 * We use this internally to build a zip file that we can use to install
 * easily via a URL using `wp plugin install <url>` which has to lead to a
 * zip file.
 *
 * This builds that Zip file for us and removes GIT.
 *
 * @since 2.0.0
 * @package  aubreypwd\wpkickstart
 *
 * This file will get removed when you run wp kickstart.
 */

namespace aubreypwd\wpkickstart\Service;

use function \aubreypwd\wpkickstart\app;

/**
 * Replaces stuff to convert this to your plugin.
 *
 * @since  2.0.0
 */
class Build_ZIP_CLI {

	/**
	 * CLI arguments.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var \WebDevStudios\CLI_Args\CLI_Args
	 */
	private $cli_args;

	/**
	 * WP File System.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @var \WP_Filesystem_Direct
	 */
	private $fs;

	/**
	 * CLI.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 * @var \WebDevStudios\CLI\CLI
	 */
	private $cli;

	/**
	 * Construct.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 */
	public function __construct() {
		if ( class_exists( '\WP_CLI' ) ) {
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';

			$this->fs = new \WP_Filesystem_Direct( true );

			$this->cli = new \WebDevStudios\CLI\CLI();

			$this->cli_args = new \WebDevStudios\CLI_Args\CLI_Args();
		}
	}

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
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
	 * Run.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @return void Early bail if not CLI.
	 */
	public function run() {
		if ( ! class_exists( '\WP_CLI' ) ) {
			return;
		}

		\WP_CLI::add_command( 'kickstart zip', [ $this, 'command' ], [
			'shortdesc' => __( 'Will help you convert the installed wpkickstart plugin into a new plugin and perform all of the search/replacements.', 'wds-migrate-subsite' ),
			'synopsis'  => [
				[
					'type'        => 'assoc',
					'name'        => 'to',
					'optional'    => true,
					'description' => __( 'Where to put the resulting .zip file e.g. `/tmp`.', 'wpkickstart' ),
				],
			],
		] );
	}

	/**
	 * WP CLI command.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  array $args       Arguments.
	 * @param  array $assoc_args Arguments.
	 */
	public function command( array $args, array $assoc_args ) {
		$this->cli_args->set_args( $args, $assoc_args ); // Ensure we have an easy way to get arguments.

		$plugindir = dirname( app()->plugin_file );

		$pluginsdir = dirname( $plugindir );

		$version = app()->get_header( 'Version' );

		$default_to = "{$pluginsdir}/wpkickstart-{$version}.zip";

		$to = ! empty( $this->cli_args->get_arg( 'to' ) ) ? $this->cli_args->get_arg( 'to' ) : $default_to;

		$this->cli->log( "Building to {$to}..." );

		$this->zipdir( $plugindir, $to );

		$this->cli->success( "Built {$to}!" );
	}

	/**
	 * Zip up a directory.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $path The path to zip up.
	 * @param  string $to   Where to zip it to.
	 */
	private function zipdir( $path, $to ) {
		$zip = new \ZipArchive();

		$zip->open( $to, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );

		$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path ) );

		foreach ( $files as $name => $file ) {
			if ( $file->isDir() ) {
				flush();
				continue;
			}

			$path = $file->getRealPath();

			if ( stristr( $path, '.git' ) ) {
				continue;
			}

			$this->cli->success( "Added {$path}" );

			$rel_path = substr( $path, strlen( $path ) + 1 );

			$zip->addFile( $path, $rel_path );
		}

		$zip->close();
	}
}
