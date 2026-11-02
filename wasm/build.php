<?php

  // Builds www/wasm/pad-bundle.json, the PAD engine as one file a browser can fetch: every
  // file under pad/ and the subsystem CSS/JS in www/assets/, keyed by their paths, which
  // www/wasm/pad-wasm.js writes into PHP's in-memory file system before it runs a template.
  //
  // Left out are the sequence subsystem's large tables - the OEIS sqlite database and the
  // generated lists over 64 KB - which make up 79 of the engine's 82 MB; the sequences that
  // read them do not work in the browser, everything else does - a large file elsewhere, the
  // emoji names or the map outlines of a tag, goes in. A file that is not UTF-8 is
  // carried base64-encoded, so JSON cannot mangle it.
  //
  //   php wasm/build.php            writes www/wasm/pad-bundle.json
  //
  // The bundle is generated, not kept in git: build it again after the engine changes.

  $home   = dirname ( __DIR__ );
  $files  = [];
  $binary = [];
  $left   = 0;

  $iterator = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( "$home/pad", FilesystemIterator::SKIP_DOTS ) );

  foreach ( $iterator as $one ) {

    $path = str_replace ( '\\', '/', $one->getPathname () );
    $name = substr ( $path, strlen ( $home ) + 1 );

    if ( str_ends_with ( $name, '.sqlite' ) or ( str_starts_with ( $name, 'pad/sequence/' ) and $one->getSize () > 65536 ) or basename ( $name ) == '.DS_Store' ) {
      $left++;
      continue;
    }

    $text = file_get_contents ( $path );

    if ( mb_check_encoding ( $text, 'UTF-8' ) )
      $files [$name] = $text;
    else
      $binary [$name] = base64_encode ( $text );

  }

  // Subsystems read these through padWidgetFile. With PAD_WASM set they embed the files
  // in the rendered page, so the preview needs neither asset URLs nor a PHP server.
  $assets = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( "$home/www/assets", FilesystemIterator::SKIP_DOTS ) );

  foreach ( $assets as $one ) {
    if ( ! $one->isFile () or ! in_array ( $one->getExtension (), [ 'css', 'js' ], TRUE ) )
      continue;
    $path = str_replace ( '\\', '/', $one->getPathname () );
    $files [substr ( $path, strlen ( $home ) + 1 )] = file_get_contents ( $path );
  }

  // The same data reader as full Live pages, installed as the browser app's library.
  $files ['apps/play/_lib/live.php'] = file_get_contents ( "$home/apps/live/_lib/live.php" );

  ksort ( $files );
  ksort ( $binary );

  // The static editor uses the same application list, themes and PAD grammar names as
  // Live. Include their data so no PHP server is needed by the page.
  define ( 'PAD', "$home/pad/" );
  define ( 'APPS', "$home/apps/" );
  define ( 'DATA', "$home/DATA/" );
  require PAD . 'lib/dirs.php';
  require PAD . 'lib/subsystem.php';
  require PAD . 'subs/themes/_lib/theme.php';
  require APPS . '_common/_lib/playground.php';

  $padRoot = '../';
  [ $grammar, $names ] = padPlayEditor ();
  $apps = require APPS . '_common/_data/padApps.php';
  $icons = [];
  $themes = [];
  $iconNames = [ 'chevron-down', 'code', 'zap' ];

  foreach ( $apps as $group )
    foreach ( $group as [ , $icon ] )
      $iconNames [] = $icon;

  foreach ( array_unique ( $iconNames ) as $icon )
    $icons [$icon] = file_get_contents ( PAD . "subs/icons/types/$icon.svg" );

  foreach ( padThemeTypes () as $name ) {
    $theme = padThemeData ( $name );
    $themes [$name] = [ 'name' => $theme ['name'], 'scheme' => $theme ['scheme'], 'css' => padThemeCss ( $name ) ];
  }

  $editor = [ 'apps' => $apps, 'columns' => require APPS . '_common/_data/padAppColumns.php',
              'icons' => $icons, 'themes' => $themes, 'grammar' => $grammar, 'names' => $names ];

  $commit = trim ( (string) @shell_exec ( 'git -C ' . escapeshellarg ( $home ) . ' rev-parse --short HEAD 2>/dev/null' ) );

  $bundle = json_encode ( [ 'built' => gmdate ( 'Y-m-d H:i:s' ) . ' UTC', 'commit' => $commit, 'files' => $files, 'base64' => (object) $binary, 'editor' => $editor ],
                          JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  if ( $bundle === FALSE )
    exit ( "build: the bundle could not be encoded: " . json_last_error_msg () . "\n" );

  file_put_contents ( "$home/www/wasm/pad-bundle.json", $bundle );

  printf ( "www/wasm/pad-bundle.json: %d files, %.1f MB, %d large sequence files left out\n",
           count ( $files ) + count ( $binary ), strlen ( $bundle ) / 1048576, $left );

?>
