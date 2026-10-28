<?php
/**
 * Check an icon folder before it ships: php tools/check-pack.php [dir] [--max-bytes=N] [--max-icons=N]
 *
 * The manifest rules are Easy SVG Pro's scanner (includes/core/manifest.php),
 * so what passes here loads there. The SVG rules are stricter on purpose: Pro
 * strips scripts and external references on import, but a pack that relies on
 * that ships files nobody should have to clean. Exit 1 on any problem.
 *
 * Plain PHP 7.4, no WordPress: CI runs it on the floor the plugin header promises.
 */

declare(strict_types=1);

$dir       = 'icons';
$max_bytes = 2097152; // Pro's SOURCE_MAX_BYTES.
$max_icons = 1000;    // Pro's SOURCE_MAX_ICONS, before easy_svg_pro_source_max_icons.
foreach ( array_slice( $argv, 1 ) as $arg ) {
	if ( 0 === strpos( $arg, '--max-bytes=' ) ) {
		$max_bytes = (int) substr( $arg, 12 );
	} elseif ( 0 === strpos( $arg, '--max-icons=' ) ) {
		$max_icons = (int) substr( $arg, 12 );
	} else {
		$dir = rtrim( $arg, '/' );
	}
}

$problems = array();
$fail     = static function ( string $where, string $reason, string $detail = '' ) use ( &$problems ): void {
	$problems[] = sprintf( 'FAIL %s: %s%s', $where, $reason, '' === $detail ? '' : " ($detail)" );
};

$name_ok = static function ( $name ): bool {
	return is_string( $name ) && 1 === preg_match( '/^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$/', $name );
};

if ( ! is_dir( $dir ) || is_link( $dir ) ) {
	fwrite( STDERR, "FAIL $dir: not a real folder\n" );
	exit( 1 );
}

// ─── The manifest ───────────────────────────────────────────────────────────

$manifest_path = "$dir/manifest.json";
$manifest      = null;
if ( ! is_file( $manifest_path ) ) {
	$fail( $manifest_path, 'manifest-missing' );
} else {
	$manifest = json_decode( (string) file_get_contents( $manifest_path ), true, 64 );
	if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $manifest ) || ( array() !== $manifest && array_keys( $manifest ) === range( 0, count( $manifest ) - 1 ) ) ) {
		$fail( $manifest_path, 'manifest-invalid', json_last_error_msg() );
		$manifest = null;
	}
}

if ( is_array( $manifest ) ) {
	$version = $manifest['version'] ?? 1;
	if ( ! is_int( $version ) || $version > 1 ) {
		$fail( $manifest_path, 'unsupported-version', 'Pro skips the whole folder; use 1' );
	}
	if ( ! $name_ok( $manifest['slug'] ?? null ) ) {
		$fail( $manifest_path, 'bad-slug', 'lower case letters, digits, - and _' );
	}
	// Optional for Pro, required here: a shared pack has to say whose drawings
	// these are, under what licence, and where they came from.
	foreach ( array( 'label', 'license', 'author', 'source' ) as $key ) {
		if ( ! isset( $manifest[ $key ] ) || ! is_string( $manifest[ $key ] ) || '' === trim( $manifest[ $key ] ) ) {
			$fail( $manifest_path, "missing-$key" );
		}
	}

	$icons = $manifest['icons'] ?? null;
	if ( ! is_array( $icons ) || array() === $icons ) {
		$fail( $manifest_path, 'manifest-invalid', 'icons is missing or empty' );
		$icons = array();
	}

	$names = array();
	$files = array();
	foreach ( $icons as $key => $entry ) {
		$entry = is_array( $entry ) ? $entry : array();
		$name  = $entry['name'] ?? ( $entry['slug'] ?? ( is_string( $key ) ? $key : null ) );
		$file  = isset( $entry['file'] ) && is_string( $entry['file'] ) ? $entry['file'] : ( is_string( $name ) ? "$name.svg" : '' );
		$where = "$manifest_path [" . ( is_string( $name ) ? $name : "#$key" ) . ']';

		if ( ! $name_ok( $name ) ) {
			$fail( $where, 'bad-name', 'Pro skips it, never rewrites it' );
		} elseif ( isset( $names[ $name ] ) ) {
			$fail( $where, 'duplicate' );
		} else {
			$names[ $name ] = true;
		}

		if ( '' === $file || '.svg' !== strtolower( substr( $file, -4 ) ) || false !== strpbrk( $file, "/\\\0" ) || false !== strpos( $file, '..' ) ) {
			$fail( $where, 'outside-folder', $file );
			continue;
		}
		if ( isset( $files[ strtolower( $file ) ] ) ) {
			$fail( $where, 'duplicate-file', $file );
		}
		$files[ strtolower( $file ) ] = true;
		if ( ! is_file( "$dir/$file" ) ) {
			$fail( $where, 'missing-file', $file );
		}
	}
}

// ─── Every SVG in the folder, listed or not: Pro reads them all ──────────────

$seen = array();
$svgs = glob( "$dir/*.svg" ) ?: array();
$svgs = array_merge( $svgs, glob( "$dir/*.SVG" ) ?: array() );
if ( array() === $svgs ) {
	$fail( $dir, 'no-icons' );
}
if ( count( $svgs ) > $max_icons ) {
	$fail( $dir, 'too-many', count( $svgs ) . " files, Pro reads $max_icons" );
}

foreach ( $svgs as $path ) {
	$lower = strtolower( basename( $path ) );
	if ( isset( $seen[ $lower ] ) ) {
		$fail( $path, 'file-name-clash', 'differs from ' . $seen[ $lower ] . ' only in case' );
	}
	$seen[ $lower ] = basename( $path );

	if ( is_link( $path ) ) {
		$fail( $path, 'symlink', 'Pro refuses links' );
		continue;
	}
	if ( filesize( $path ) > $max_bytes ) {
		$fail( $path, 'too-large', filesize( $path ) . " bytes, limit $max_bytes" );
		continue;
	}

	$bytes = (string) file_get_contents( $path );
	if ( 1 === preg_match( '/<!ENTITY/i', $bytes ) ) {
		$fail( $path, 'entity', 'DTD entities can smuggle markup past a sanitiser' );
	}

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$loaded = $doc->loadXML( $bytes, LIBXML_NONET );
	libxml_clear_errors();
	if ( ! $loaded || null === $doc->documentElement || 'svg' !== strtolower( (string) $doc->documentElement->localName ) ) {
		$fail( $path, 'not-svg' );
		continue;
	}

	$found = array();
	foreach ( $doc->getElementsByTagName( '*' ) as $el ) {
		$tag = strtolower( (string) $el->localName );
		if ( 'script' === $tag ) {
			$found['script'] = '<script>';
		}
		if ( 'style' === $tag ) {
			$css = (string) $el->textContent;
			if ( false !== stripos( $css, '@import' ) ) {
				$found['external-reference'] = '@import';
			}
			foreach ( css_urls( $css ) as $url ) {
				$found['external-reference'] = "url($url)";
			}
		}
		foreach ( $el->attributes ?? array() as $attr ) {
			$attr_name = strtolower( (string) $attr->localName );
			$value     = (string) $attr->value;
			if ( 0 === strpos( $attr_name, 'on' ) ) {
				$found['event-handler'] = $attr_name;
			}
			if ( false !== strpos( strtolower( (string) preg_replace( '/[\s\x00-\x1f]+/', '', $value ) ), 'javascript:' ) ) {
				$found['javascript-url'] = $attr_name;
			}
			if ( in_array( $attr_name, array( 'href', 'src' ), true ) && '#' !== substr( ltrim( $value ), 0, 1 ) ) {
				$found['external-reference'] = $value;
			}
			foreach ( css_urls( $value ) as $url ) {
				$found['external-reference'] = "url($url)";
			}
		}
	}
	foreach ( $found as $reason => $detail ) {
		$fail( $path, $reason, $detail );
	}
}

/**
 * The url(...) targets in a piece of CSS that do not point inside the file.
 *
 * @return array<int,string>
 */
function css_urls( string $css ): array {
	preg_match_all( '/url\(\s*[\'"]?([^\'")]*)/i', $css, $m );
	return array_values( array_filter( $m[1], static function ( string $url ): bool {
		return '#' !== substr( trim( $url ), 0, 1 );
	} ) );
}

foreach ( $problems as $line ) {
	fwrite( STDERR, $line . "\n" );
}
printf( "%d icons checked in %s, %d problems\n", count( $svgs ), $dir, count( $problems ) );
exit( array() === $problems ? 0 : 1 );
