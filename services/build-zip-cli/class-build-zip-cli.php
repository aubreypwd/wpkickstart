<?php
/**
 * Build a zip for easy installation.
 *
 * @since 2.0.0
 * @package  CompanyNamespace\ProjectNamespace
 *
 * This file will get removed when you run wp kickstart.
 */

namespace CompanyNamespace\ProjectNamespace\Service;

use function \CompanyNamespace\ProjectNamespace\app;

/**
 * Replaces stuff to convert this to your plugin.
 *
 * @since  2.0.0
 */
class Build_ZIP_CLI {

	/**
	 * Line removals.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $line_removals = [
		'app/class-app.php' => [ 396, 397, 398, 399, 400, 401 ],
	];

	/**
	 * File removals.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $file_removals = [
		'components/cli',
		'components/cli-args',
		'components/example-component',
		'services/example-service',
		'services/replace-cli',
	];

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
	 * Only work on these extensions.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $extensions = [
		'.php',
		'.md',
		'.js',
		'.json',
	];

	/**
	 * Directories to ignore modifications.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
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

		\WP_CLI::add_command( 'build kickstart', [ $this, 'command' ], [
			'shortdesc' => __( 'Will help you convert the installed wpkickstart plugin into a new plugin and perform all of the search/replacements.', 'wds-migrate-subsite' ),
			'synopsis'  => [
				[
					'type'        => 'assoc',
					'name'        => 'to',
					'optional'    => false,
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

		$tmpdir = untrailingslashit( sys_get_temp_dir() );

		$tmpplugindir = "{$tmpdir}/wpkickstart";

		error_log( print_r( (object) array(
			'line' => __LINE__,
			'file' => __FILE__,
			'dump' => array(
				$tmpplugindir,
			),
		), true ) );

		if ( file_exists( $tmpplugindir ) ) {
			$this->fs->rmdir( $tmpplugindir, true );
		}

		$this->fs->mkdir( $tmpplugindir );

		$zipfile = "{$tmpdir}/wpkickstart.zip";

		if ( file_exists( $zipfile ) ) {
			$this->fs->delete( $zipfile );
			return;
		}

		$plugindir = dirname( app()->plugin_file );

		$this->fs->copy( $plugindir, $tmpplugindir );

		// Copy our plugin to a new location.
		// exec( "copy -Rfva {$plugindir} {$tmpplugindir}");

	}
}
