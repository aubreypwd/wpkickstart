<?php

$args = array();
foreach ( $argv as $arg ) {
	$arg = explode( '=', $arg );
	$args[ $arg[0] ] = isset( $arg[1] ) ? $arg[1] : '';
}

foreach ( $args as $i => $value ) {
	$args[ str_replace( '--', '', $i ) ] = $value;
}

error_log( print_r( (object) array(
	'line' => __LINE__,
	'file' => __FILE__,
	'dump' => array(
		$args,
	),
), true ) );

foreach ( array(
	'MyPluginName',
	'1.0.0',
	'plugin-name',
	'WebDevStudios',
	'MyPluginName',
	'Aubrey Portwood',
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
		'MyPluginName'      => $args[ 'MyPluginName' ],
		'1.0.0'            => $args[ '1.0.0' ],
		'plugin-name'     => $args[ 'plugin-name' ],
		'WebDevStudios' => $args[ 'WebDevStudios' ],
		'MyPluginName'  => $args[ 'MyPluginName' ],
		'Aubrey Portwood'        => $args[ 'Aubrey Portwood' ],
	);

	foreach ( $replacements as $replace => $with ) {
		$contents = str_ireplace( $replace, $with, $contents );
	}

	file_put_contents( $filename, $contents );

}
