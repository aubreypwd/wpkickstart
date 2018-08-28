<?php

if ( php_sapi_name() !== "cli" ) {
	die( 'Not allowed, run via cli.' );
}

$args = array();
foreach ( $argv as $arg ) {
	$arg = explode( '=', $arg );
	$args[ $arg[0] ] = isset( $arg[1] ) ? $arg[1] : '';
}

foreach ( $args as $i => $value ) {
	$args[ str_replace( '--', '', $i ) ] = $value;
}

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

$dir = new RecursiveDirectoryIterator( str_replace( basename( __FILE__ ), '', __FILE__ ) );
foreach ( new RecursiveIteratorIterator( $dir ) as $filename => $file ) {
	$ignores = array(
		'.git/',
		'.gitignore',
		'.xml',
		'/..',
		'/.',
		'replace.php',
		'-lock',
	);

	if ( is_dir( $filename ) ) {
		continue;
	}

	$skip = false;
	foreach ( $ignores as $ignore ) {
		if ( stristr( $filename, $ignore ) ) {
			$skip = true;
		}
	}

	if ( $skip ) {
		continue;
	}

	$contents = file_get_contents( $filename );

	$replacements = array(
		'__PluginName__'      => $args[ '__PluginName__' ],
		'__NEXT__'            => $args[ '__NEXT__' ],
		'__plugin-name__'     => $args[ '__plugin-name__' ],
		'__YourCompanyName__' => $args[ '__YourCompanyName__' ],
		'__YourPluginName__'  => $args[ '__YourPluginName__' ],
		'__YourName__'        => $args[ '__YourName__' ],
		'__your-company__'    => $args[ '__your-company__' ],
	);

	foreach ( $replacements as $replace => $with ) {
		$contents = str_ireplace( $replace, $with, $contents );
	}

	file_put_contents( $filename, $contents );
}


copy( 'wp-plugin-boilerplate.php', $args[ '__plugin-name__' ] . '.php' );

unlink( 'wp-plugin-boilerplate.php' );
