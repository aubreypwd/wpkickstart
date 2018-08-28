<?php // @codingStandardsIgnoreStart:

if ( php_sapi_name() !== "cli" ) {
	die( 'Not allowed, run via cli.' );
}

$args = array();
foreach ( $argv as $arg ) {

	// Get args together.
	$arg = explode( '=', $arg );
	$args[ $arg[0] ] = isset( $arg[1] ) ? $arg[1] : '';
}

foreach ( $args as $i => $value ) {

	// Make sure we're not dealing with the -- part.
	$args[ str_replace( '--', '', $i ) ] = $value;
}

// Check all args required are there.
foreach ( array(
	'__PluginName__',
	'__NEXT__',
	'__plugin-name__',
	'__YourCompanyName__',
	'__YourPluginName__',
	'__YourName__',
	'__your-company__',
) as $r_arg ) {
	if ( ! isset( $args[ $r_arg ] ) ) {
		die( "Sorry, but `php replace.php --{$r_arg}=value` is required." );
	}
}

// Loop through all files.
$dir = new RecursiveDirectoryIterator( str_replace( basename( __FILE__ ), '', __FILE__ ) );
foreach ( new RecursiveIteratorIterator( $dir ) as $filename => $file ) {
	$ignores = array(
		'.git/', // Except .git/*
		'.gitignore', // And this.
		'.xml', // Why would we do this.
		'/..', // These suck.
		'/.', // This too.
		'replace.php', // And this file duh.
		'-lock', // and package.lock.
	);

	if ( is_dir( $filename ) ) {

		// Directories don't care.
		continue;
	}

	$skip = false;
	foreach ( $ignores as $ignore ) {
		if ( stristr( $filename, $ignore ) ) {

			// Skip this file we want to ignore it.
			$skip = true;
		}
	}

	if ( $skip ) {

		// Do what ⬆️ he said.
		continue;
	}

	// Replace these things.
	$replacements = array(
		'__PluginName__'      => $args[ '__PluginName__' ],
		'__NEXT__'            => $args[ '__NEXT__' ],
		'__plugin-name__'     => $args[ '__plugin-name__' ],
		'__YourCompanyName__' => $args[ '__YourCompanyName__' ],
		'__YourPluginName__'  => $args[ '__YourPluginName__' ],
		'__YourName__'        => $args[ '__YourName__' ],
		'__your-company__'    => $args[ '__your-company__' ],
	);

	// Get the contents of the file.
	$contents = file_get_contents( $filename );
	foreach ( $replacements as $replace => $with ) {

		// Replace the thing.
		$contents = str_ireplace( $replace, $with, $contents );
	}

	// Save the file.
	file_put_contents( $filename, $contents );
}

// Rename the base file.
copy( 'wp-plugin-boilerplate.php', $args[ '__plugin-name__' ] . '.php' );

// Delete the old one.
unlink( 'wp-plugin-boilerplate.php' );

// 🤠
