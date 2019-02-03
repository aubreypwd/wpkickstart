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

namespace aubreypwd\wpkickstart;

/**
 * Replaces stuff to convert this to your plugin.
 *
 * @since  2.0.0
 */
class Release_CLI {

	/**
	 * CLI arguments.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var \aubreypwd\WP_KickStart_Components\CLI_Args
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
	 * @var aubreypwd\WP_KickStart_Components\CLI
	 */
	private $cli;

	/**
	 * Directories to ignore.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @var array
	 */
	private $ignore_relative_dirs = [
		'/vendor',
		'/dist',
	];

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

			$this->cli = new \aubreypwd\WP_KickStart_Components\CLI();

			$this->cli_args = new \aubreypwd\WP_KickStart_Components\CLI_Args();
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

		\WP_CLI::add_command( 'kickstart release', [ $this, 'command' ], [
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

		$default_to = "{$plugindir}/dist/wpkickstart.zip";

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
		$path = realpath( $path );

		$plugin_dir = dirname( app()->plugin_file );

		$dirto = dirname( $to );

		if ( ! file_exists( $dirto ) ) {

			// @codingStandardsIgnoreLine: Want to stop notices.
			@$this->fs->mkdir( $dirto );
		}

		$zip = new \ZipArchive();

		$zip->open( $to, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );

		$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path ) );

		foreach ( $files as $name => $file ) {
			if ( $file->isDir() ) {
				flush();
				continue;
			}

			$file_path = $file->getRealPath();

			$lslash_relative_path = str_replace( $plugin_dir, '', $file_path );

			if ( stristr( $lslash_relative_path, '.git' ) ) {
				continue; // Anything git.
			}

			if ( stristr( $lslash_relative_path, 'dist/' ) ) {
				continue;
			}

			if ( stristr( $lslash_relative_path, '.DS_Store' ) ) {
				continue; // Any .DS_Store.
			}

			if ( stristr( $lslash_relative_path, 'node_modules/' ) ) {
				continue; // Any node_modules.
			}

			$parts = explode( '/', dirname( $lslash_relative_path ) );

			$lslash_relative_base = '/' . trim( $parts[1] );

			if ( in_array( $lslash_relative_base, array_values( $this->ignore_relative_dirs ), true ) ) {
				continue;
			}

			$relative_path = substr( $file_path, strlen( $path ) + 1 );

			$this->cli->log( "Added {$lslash_relative_path}" );

			$zip->addFile( $file_path, $relative_path );
		}

		$zip->close();
	}
}
