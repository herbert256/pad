<?php

  // Builds www/wasm/pad-bundle.json, the PAD engine as one file a browser can fetch: every
  // file under pad/ keyed by its path, which www/wasm/pad-wasm.js writes into the in-memory
  // file system of PHP compiled to WebAssembly before it runs a template.
  //
  // Left out are the sequence subsystem's large tables - the OEIS sqlite database and the
  // generated lists over 64 KB - which make up 79 of the engine's 82 MB; the sequences that
  // read them do not work in the browser, everything else does. A file that is not UTF-8 is
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

    if ( str_ends_with ( $name, '.sqlite' ) or $one->getSize () > 65536 or basename ( $name ) == '.DS_Store' ) {
      $left++;
      continue;
    }

    $text = file_get_contents ( $path );

    if ( mb_check_encoding ( $text, 'UTF-8' ) )
      $files [$name] = $text;
    else
      $binary [$name] = base64_encode ( $text );

  }

  ksort ( $files );
  ksort ( $binary );

  $commit = trim ( (string) @shell_exec ( 'git -C ' . escapeshellarg ( $home ) . ' rev-parse --short HEAD 2>/dev/null' ) );

  $bundle = json_encode ( [ 'built' => gmdate ( 'Y-m-d H:i:s' ) . ' UTC', 'commit' => $commit, 'files' => $files, 'base64' => (object) $binary ],
                          JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  if ( $bundle === FALSE )
    exit ( "build: the bundle could not be encoded: " . json_last_error_msg () . "\n" );

  file_put_contents ( "$home/www/wasm/pad-bundle.json", $bundle );

  printf ( "www/wasm/pad-bundle.json: %d files, %.1f MB, %d large sequence files left out\n",
           count ( $files ) + count ( $binary ), strlen ( $bundle ) / 1048576, $left );

?>
