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

class Replace_CLI {
	private $line_removals = [
		'app/class-app.php' => [ 394, 395, 396, 397, 398, 399 ],
	];

	private $file_removals = [];

	private $cli_args;

	private $extensions = [
		'.php',
		'.md',
		'.js',
	];

	private $ignore_dirs = [
		'vendor',
	];

	private $fs;

	public function hooks() {
		if ( ! class_exists( '\WP_CLI' ) ) {
			return;
		}
	}

	public function __construct() {
		if ( ! class_exists( '\WP_CLI' ) ) {
			return;
		}

		require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';

		$this->fs = new \WP_Filesystem_Direct( true );

		$this->cli_args = new \WebDevStudios\CLI_Args\CLI_Args();
	}

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

	public function kickstart( array $args, array $assoc_args ) {
		$this->cli_args->set_args( $args, $assoc_args ); // Ensure we have an easy way to get arguments.

		$this->remove_lines();
	}

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

			if ( ! in_array( $relative_file, array_keys( $this->line_removals ) ) ) {
				continue;
			}

			$lines = $this->line_removals[ $relative_file ];

			$file_content_array = $this->fs->get_contents_array( $file );

			foreach ( $lines as $line ) {
				if ( ! isset( $file_content_array[ $line ] ) ) {
					throw new \Exception( "{$line} is not in {$file}." );
				}

				// Remove that line.
				unset( $file_content_array[ $line ] );
			}

			file_put_contents( $file, $file_content_array );
		}
	}

	private function ignore( string $file ) {
		foreach ( $this->ignore_dirs as $ignore_dir ) {
			if ( stristr( $file, trailingslashit( $ignore_dir ) ) ) {
				return true;
			}
		}

		return false;
	}

	private function has_valid_extension( string $file ) {
		foreach ( $this->extensions as $extension ) {
			if ( stristr( $file, $extension ) ) {
				return true;
			}
		}

		return false;
	}
}
