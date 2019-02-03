<?php
/**
 * Command line replacements.
 *
 * @since 2.0.0
 * @package  aubreypwd\wpkickstart
 *
 * This file will get removed when you run wp kickstart build.
 */

namespace aubreypwd\wpkickstart\Service;

use function \aubreypwd\wpkickstart\app;

/**
 * Replaces stuff to convert this to your plugin.
 *
 * @since  2.0.0
 */
class Build_CLI {

	/**
	 * Line removals.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $line_removals = [
		'app/class-app.php' => [ 396, 397, 398, 399, 400, 401, 402, 403, 404 ],
		'composer.json'     => [ 14 ],
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
		'services/build-cli', // Our own build process.
		'services/release-cli', // Our own release process.
		'dist', // Any dist files.
		'vendor', // Composers vendor library.
		'composer.lock',
	];

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
	 * Is this a dry run?
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @var boolean
	 */
	private $dryrun = false;

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
	 * @var \aubreypwd\WP_KickStart_Components\CLI
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

			$this->cli = new \aubreypwd\WP_KickStart_Components\CLI();

			$this->cli_args = new \aubreypwd\WP_KickStart_Components\CLI_Args();

			$this->dryrun = defined( 'WPKICKSTART_DRY_RUN' ) && WPKICKSTART_DRY_RUN ? true : false;
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

		add_action( 'wp_kickstart_file', [ $this, 'remove_lines' ] );
		add_action( 'wp_kickstart_file', [ $this, 'remove_file' ] );
		add_action( 'wp_kickstart_file', [ $this, 'replace_strings' ] );
		add_action( 'wp_kickstart_file', [ $this, 'rename_plugin_file' ] ); // Must be last!
	}

	/**
	 * Rename the plugin file.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 */
	public function rename_plugin_file( $file ) {
		if ( is_dir( $file ) ) {
			return;
		}

		if ( ! $this->is_code_file( $file ) ) {
			return;
		}

		// Only when the file is the kickstart file.
		if ( 'wpkickstart.php' === basename( $file ) ) {
			$dir = untrailingslashit( dirname( $file ) );

			$slug = $this->slugify( $this->cli_args->get_arg( 'name' ) );

			if ( ! $this->dryrun ) {
				$this->fs->move( $file, "{$dir}/{$slug}.php" );
			}
		}
	}

	/**
	 * Make string replacements.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return void         Early bail if file does not exist or is empty.
	 */
	public function replace_strings( $file ) {
		if ( is_dir( $file ) ) {
			return;
		}

		if ( ! $this->is_code_file( $file ) ) {
			return;
		}

		$replacements = $this->get_replacements();

		if ( ! file_exists( $file ) ) {
			return;
		}

		$file_contents = file_get_contents( $file ); // @codingStandardsIgnoreLine: We want this.

		foreach ( $replacements as $search => $replace ) {
			$file_contents = str_replace( $search, $replace, $file_contents );
		}

		if ( ! $this->dryrun ) {
			file_put_contents( $file, $file_contents ); // @codingStandardsIgnoreLine: We want this.
		}
	}

	/**
	 * Collect the replacements.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @return array The replacements.
	 */
	private function get_replacements() {
		static $cached;

		if ( ! is_array( $cached ) ) {
			$name = $this->cli_args->get_arg( 'name' );

			$author = $this->cli_args->get_arg( 'author' );

			$website = esc_url( $this->cli_args->get_arg( 'website' ) );

			$company_slug = $this->slugify( $this->cli_args->get_arg( 'company' ) );

			$plugin_slug = $this->slugify( $name );

			$description = $this->cli_args->get_arg( 'description' );

			$classy_company = $this->classyfy( $this->cli_args->get_arg( 'company' ) );

			$classy_name = $this->classyfy( $name );

			$namespace = "namespace {$classy_company}" . '\\' . $classy_name;

			// @codingStandardsIgnoreStart: Alignments below don't pass CS ¯\_(ツ)_/¯.
			$cached = [

				// Plugin file.
				'Plugin Name: wpkickstart'                                                                                         => "Plugin Name: {$name}",
				'A great way to kickstart a new WordPress plugin. Just activate and run <code>wp kickstart</code> to get started.' => $this->cli_args->get_arg( 'description' ),
				'Author:      Aubrey Portwood'                                                                                     => "Author:      {$author}",
				'Author URI:  http://github.com/aubreypwd/wpkickstart'                                                             => "Author URI:  {$website}",

				// Other stuff.
				'2.0.0'                                                                                                            => $this->cli_args->get_arg( 'since' ),
				'x.x.x'                                                                                                            => $this->cli_args->get_arg( 'since' ),
				'Your Name <your@email.com>'                                                                                       => $author,
				'project-slug'                                                                                                     => $plugin_slug,
				'namespace aubreypwd\wpkickstart'                                                                                  => $namespace,
				'Company Name'                                                                                                     => $this->cli_args->get_arg( 'company' ),
				'company-slug'                                                                                                     => $this->slugify( $this->cli_args->get_arg( 'company' ) ),
				'Project Description'                                                                                              => $description,
				'http://your-website.com'                                                                                          => $website,
				'Aubrey Portwood <aubreypwd@icloud.com>'                                                                           => $author,

				// @package
				'aubreypwd\wpkickstart\\'                                                                                          => "{$classy_company}" . '\\' . $classy_name . '\\',
				'@package  aubreypwd\wpkickstart'                                                                                  => "@package  {$classy_company}" . '\\' . $classy_name,
				'@package aubreypwd\wpkickstart'                                                                                   => "@package {$classy_company}" . '\\' . $classy_name,
				'@package     aubreypwd\wpkickstart'                                                                               => "@package     {$classy_company}" . '\\' . $classy_name,

				// Composer.json
				'"name": "aubreypwd/wpkickstart",'                                                                                 => str_replace( 'wpkickstart', $plugin_slug, str_replace( 'aubreypwd', $company_slug, '"name": "aubreypwd/wpkickstart",' ) ),
				'https://github.com/aubreypwd/wpkickstart/issues'                                                                  => $website,
				'https://github.com/aubreypwd/wpkickstart'                                                                         => $website,
				'"description": "",'                                                                                               => str_replace( '""', "\"{$description}\"", '"description": "",' ),
				'"oomphinc/composer-installers-extender": "*",'                                                                    => '"oomphinc/composer-installers-extender": "*"', // Removes comma.
			];
			// @codingStandardsIgnoreEnd
		}

		return $cached;
	}

	/**
	 * Convert a string to a slug.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
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
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $string The string.
	 * @return string         The ClassFormat format.
	 */
	private function classyfy( $string ) {
		$spaced = str_replace( '-', ' ', $this->slugify( $string ) );

		$ucwords = ucwords( $spaced );

		return str_replace( ' ', '_', $ucwords );
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

		\WP_CLI::add_command( 'kickstart build', [ $this, 'command' ], [
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
					'description' => __( 'What @author will say, e.g. `Aubrey Portwood <aubreypwd@icloud.com>`.', 'wpkickstart' ),
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
				[
					'type'        => 'assoc',
					'name'        => 'website',
					'optional'    => false,
					'description' => __( 'Your URL, e.g. `http://example.com`.', 'wpkickstart' ),
				],
				[
					'type'        => 'assoc',
					'name'        => 'description',
					'optional'    => false,
					'description' => __( 'Your applications description, what does it do?.', 'wpkickstart' ),
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

		// @codingStandardsIgnoreLine: Deactivate our plugin.
		shell_exec( 'wp plugin deactivate wpkickstart/wpkickstart.php --allow-root' );

		$this->loop_through_files_and_fire_hook();

		$this->finalize();

		$plugin_slug = $this->slugify( $this->cli_args->get_arg( 'name' ) );

		$this->cli->success( "Done! Your new is in plugins/{$plugin_slug} and has been activated and is ready to be worked on." );
	}

	/**
	 * Move the new plugin and activate it.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 */
	private function finalize() {

		// Remove git before it's moved.
		$plugin_dir = untrailingslashit( dirname( app()->plugin_file ) );

		if ( ! $this->dryrun ) {
			$this->fs->delete( "{$plugin_dir}/.git", true ); // Remove git.
			$this->fs->delete( "{$plugin_dir}/.gitignore", true ); // Remove gitignore.
		}

		// Move it.
		$slug = $this->slugify( $this->cli_args->get_arg( 'name' ) );

		$plugins_dir = untrailingslashit( dirname( dirname( app()->plugin_file ) ) );

		$olddir = dirname( app()->plugin_file );
		$newdir = "{$plugins_dir}/{$slug}";

		if ( ! file_exists( $newdir ) ) {
			if ( ! $this->dryrun ) {
				$this->fs->move( $olddir, $newdir );
			}
		}

		if ( ! $this->dryrun ) {
			// @codingStandardsIgnoreLine: Try and activate that plugin.
			shell_exec( "wp plugin activate {$slug} --allow-root" );
		}
	}

	/**
	 * Remove a file.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return void         Early bail if already done.
	 */
	public function remove_file( $file ) {
		$plugin_dir = dirname( app()->plugin_file );

		$dir = dirname( $file );

		if ( ! file_exists( $dir ) ) {
			return; // The directory doesn't even exist, already done.
		}

		$relative_dir = $this->get_relative_file( $dir );

		$relative_file = $this->get_relative_file( $file );

		if ( stristr( $relative_file, '.git' ) ) {
			if ( ! $this->dryrun ) {
				if ( is_dir( $relative_dir ) ) {
					$this->delete( $relative_dir, true );
				} else {
					$this->delete( $relative_file );
				}
			}

			return;
		}

		if ( stristr( $relative_file, 'components/vendor/' ) ) {
			if ( ! $this->dryrun ) {
				$this->fs->delete( "{$plugin_dir}/components/vendor", true );
			}

			return;
		}

		if ( in_array( $relative_dir, $this->file_removals, true ) ) {
			if ( ! $this->dryrun ) {
				$this->fs->delete( $dir, true );
			}

			return;
		}

		if ( ! file_exists( $file ) ) {
			return; // Somehow file is deleted already.
		}

		if ( in_array( $relative_file, $this->file_removals, true ) ) {
			if ( ! $this->dryrun ) {
				$this->fs->delete( $file );
			}

			return;
		}
	}

	/**
	 * Get a relative version of a file.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return string       The relative file.
	 */
	private function get_relative_file( $file ) {
		return ltrim( str_replace( dirname( app()->plugin_file ), '', $file ), '/' );
	}

	/**
	 * Loop through our files and pass it to a hook.
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 */
	private function loop_through_files_and_fire_hook() {
		$plugin_dir = dirname( app()->plugin_file );

		$recursive_dir = new \RecursiveDirectoryIterator( $plugin_dir );

		foreach ( new \RecursiveIteratorIterator( $recursive_dir ) as $file => $file_obj ) {
			if ( ! is_string( $file ) ) {
				continue;
			}

			if ( ! app()->is_our_file( $file ) ) {
				continue;
			}

			/**
			 * Do something to this file.
			 *
			 * @author Aubrey Portwood <aubreypwd@icloud.com>
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
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param string $file The file.
	 *
	 * @throws \Exception If we can't remove a line you've specified.
	 * @return void       Early bail if not a file to remove lines.
	 */
	public function remove_lines( $file ) {
		if ( is_dir( $file ) ) {
			return;
		}

		if ( ! $this->is_code_file( $file ) ) {
			return;
		}

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

		if ( ! $this->dryrun ) {
			// @codingStandardsIgnoreLine: We want this, it's cheap and works with an array.
			file_put_contents( $file, $file_content_array );
		}
	}

	/**
	 * Does a file have a valid extension?
	 *
	 * @author Aubrey Portwood <aubreypwd@icloud.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return boolean      True if it does, false if not.
	 */
	private function has_valid_extension( $file ) {
		foreach ( $this->extensions as $extension ) {
			if ( stristr( $file, $extension ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Is a file a "code" file?
	 *
	 * @author Aubrey Portwood <aubrey@webdevstudios.com>
	 * @since  2.0.0
	 *
	 * @param  string $file The file.
	 * @return boolean      True if it is, false if not.
	 */
	private function is_code_file( $file ) {
		if ( stristr( $file, '.php' ) ) {
			return true;
		}

		if ( stristr( $file, '.js' ) ) {
			return true;
		}

		if ( stristr( $file, '.css' ) ) {
			return true;
		}

		if ( stristr( $file, '.json' ) ) {
			return true;
		}

		if ( stristr( $file, '.md' ) ) {
			return true;
		}

		return false;
	}
}
