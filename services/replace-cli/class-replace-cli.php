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
		'.json',
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

		add_action( 'wp_kickstart_file', [ $this, 'remove_lines' ] );
		add_action( 'wp_kickstart_file', [ $this, 'remove_file' ] );
		add_action( 'wp_kickstart_file', [ $this, 'replace_strings' ] );
	}

	/**
	 * Make string replacements.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 */
	public function replace_strings( string $file ) {
		$replacements = $this->get_replacements();

		$file_contents = file_get_contents( $file ); // @codingStandardsIgnoreLine: We want this.

		foreach ( $replacements as $search => $replace ) {
			$file_contents = str_replace( $search, $replace, $file_contents );
		}

		file_put_contents( $file, $file_contents ); // @codingStandardsIgnoreLine: We want this.
	}

	/**
	 * Collect the replacements.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @return array The replacements.
	 */
	private function get_replacements() {
		static $cached;

		if ( ! is_array( $cached ) ) {
			$cached = [
				'2.0.0'                                      => $this->cli_args->get_arg( 'since' ),
				'__NEXT__'                                   => $this->cli_args->get_arg( 'since' ),
				'__YourName__'                               => $this->cli_args->get_arg( 'author' ),
				'__PluginName__'                             => $this->cli_args->get_arg( 'name' ),
				'__plugin-name__'                            => $this->slugify( $this->cli_args->get_arg( 'name' ) ),
				'__YourCompanyName__'                        => $this->classify( $this->cli_args->get_arg( 'company' ) ),
				'__YourPluginName__'                         => $this->classify( $this->cli_args->get_arg( 'name' ) ),
				'__your-company__'                           => $this->slugify( $this->cli_args->get_arg( 'company' ) ),
				'Aubrey Portwood <aubrey@webdevstudios.com>' => $this->cli_args->get_arg( 'author' ),
			];
		}

		return $cached;
	}

	/**
	 * Convert a string to a slug.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $string The string.
	 * @return string         The slug.
	 */
	private function slugify( $string ) {
		return sanitize_title_with_dashes( $string );
	}

	/**
	 * Convert this to a class format.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $string The string.
	 * @param  array  $strip  What to strip (unused).
	 * @return string         The ClassFormat format.
	 */
	private function classify( $string, $strip = [] ) {
		$string = preg_replace( '/[^a-z0-9' . implode( '', $strip ) . ']+/i', ' ', $string );
		$string = trim( $string );
		$string = ucwords( $string );
		$string = str_replace( ' ', '', $string );
		$string = lcfirst( $string );

		return ucwords( $string );
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
				[
					'type'        => 'assoc',
					'name'        => 'author',
					'optional'    => false,
					'description' => __( 'What @author will say, e.g. `Aubrey Portwood <aubrey@webdevstudios.com>`.', 'wpkickstart' ),
				],
				[
					'type'        => 'assoc',
					'name'        => 'name',
					'optional'    => false,
					'description' => __( 'What is your plugin name? E.g. `My Awesome Plugin``.', 'wpkickstart' ),
				],
				[
					'type'        => 'assoc',
					'name'        => 'company',
					'optional'    => false,
					'description' => __( 'What is your company name? E.g. `My Company``.', 'wpkickstart' ),
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
		$this->loop_through_files_and_fire_hook();
	}

	/**
	 * Remove a file.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return void         Early bail if already done.
	 */
	public function remove_file( string $file ) {
		$plugin_dir = dirname( app()->plugin_file );

		$dir = dirname( $file );

		$relative_dir = ltrim( str_replace( $plugin_dir, '', $dir ), '/' );

		$relative_file = ltrim( str_replace( $plugin_dir, '', $file ), '/' );

		if ( ! file_exists( $dir ) ) {
			return; // The directory doesn't even exist, already done.
		}

		if ( in_array( $relative_dir, $this->file_removals, true ) ) {
			$this->fs->delete( $dir, true );
			return;
		}

		if ( ! file_exists( $file ) ) {
			return; // Somehow file is deleted already.
		}

		if ( in_array( $relative_file, $this->file_removals, true ) ) {
			$this->fs->delete( $file );
		}
	}

	/**
	 * Loop through our files and pass it to a hook.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 */
	private function loop_through_files_and_fire_hook() {
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

			/**
			 * Do something to this file.
			 *
			 * @author Aubrey Portwood <aubrey@webdevstudios.com>
			 * @since  2.0.0
			 *
			 * @param string $file       The file.
			 */
			do_action( 'wp_kickstart_file', $file );
		}
	}

	/**
	 * Remove specific lines from files.
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param string $file The file.
	 *
	 * @throws \Exception If we can't remove a line you've specified.
	 * @return void       Early bail if not a file to remove lines.
	 */
	public function remove_lines( $file ) {
		$plugin_dir = dirname( app()->plugin_file );

		$relative_file = ltrim( str_replace( $plugin_dir, '', $file ), '/' );

		if ( ! in_array( $relative_file, array_keys( $this->line_removals ), true ) ) {
			return;
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
