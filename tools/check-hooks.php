<?php
/**
 * Every Easy SVG hook and function a snippet uses must exist in the plugins.
 *
 *     php tools/check-hooks.php path/to/easy-svg path/to/easy-svg-pro
 *
 * Run it against the tags you support before a release of this repository.
 */

declare(strict_types=1);

if ( $argc < 3 ) {
	fwrite( STDERR, "usage: php tools/check-hooks.php <easy-svg dir> <easy-svg-pro dir>\n" );
	exit( 2 );
}

function php_files( string $dir ): array {
	$out = array();
	$it  = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $it as $file ) {
		$path = (string) $file;
		if ( str_ends_with( $path, '.php' ) && ! preg_match( '#/(vendor|node_modules|tests|e2e)/#', $path ) ) {
			$out[] = $path;
		}
	}
	return $out;
}

$source = '';
foreach ( array_slice( $argv, 1, 2 ) as $dir ) {
	foreach ( php_files( $dir ) as $file ) {
		$source .= file_get_contents( $file );
	}
}

$root = dirname( __DIR__ );
$used = array();
foreach ( array_merge( php_files( $root . '/snippets' ), php_files( $root . '/templates' ) ) as $file ) {
	$code = (string) file_get_contents( $file );
	preg_match_all( "/add_(?:filter|action)\(\s*'((?:easy_svg|esw_svg)[a-z_]*)'/", $code, $hooks );
	foreach ( $hooks[1] as $hook ) {
		$used[ 'hook ' . $hook ] = basename( $file );
	}
	// Tokens, so a hook signature in a doc comment is not read as a call.
	$tokens = array_values( array_filter( token_get_all( $code ), static fn( $t ) => ! is_array( $t ) || T_WHITESPACE !== $t[0] ) );
	foreach ( $tokens as $i => $t ) {
		if ( ! is_array( $t ) || T_STRING !== $t[0] ) {
			continue;
		}
		if ( str_starts_with( $t[1], 'easy_svg_' ) && '(' === ( $tokens[ $i + 1 ] ?? null ) ) {
			$used[ 'function ' . $t[1] ] = basename( $file );
		} elseif ( str_starts_with( $t[1], 'EASY_SVG_' ) ) {
			$used[ 'constant ' . $t[1] ] = basename( $file );
		}
	}
	// defined( 'EASY_SVG_…' ) names a constant in a string.
	preg_match_all( "/defined\(\s*'(EASY_SVG_[A-Z_]+)'/", $code, $consts );
	foreach ( $consts[1] as $const ) {
		$used[ 'constant ' . $const ] = basename( $file );
	}
}

$missing = 0;
ksort( $used );
foreach ( $used as $what => $where ) {
	[ $kind, $name ] = explode( ' ', $what, 2 );
	$q     = preg_quote( $name, '/' );
	$found = match ( $kind ) {
		'hook'     => 1 === preg_match( "/(?:apply_filters|do_action|source_filter)\(\s*'$q'/", $source ),
		'function' => 1 === preg_match( "/function\s+$q\s*\(/", $source ),
		'constant' => 1 === preg_match( "/define\(\s*'$q'|const\s+$q\s*=/", $source ),
	};
	echo ( $found ? 'ok   ' : 'MISSING ' ) . "$what ($where)\n";
	$missing += $found ? 0 : 1;
}
exit( $missing ? 1 : 0 );
