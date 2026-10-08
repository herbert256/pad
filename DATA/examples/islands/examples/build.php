<?php

  // What {vite 'src/main.js'} wrote into this page's <head>, and where it came from: the
  // manifest of the build - every source and the file it became - or the dev server, while it
  // runs. The clock is a Solid island started from the server's time.

  $dev      = padViteDev ();
  $manifest = $dev === '' ? ( padViteManifest () ?? [] ) : [];
  $written  = trim ( padViteTags ( [ 'src/main.js' ], FALSE, FALSE ) );

  $sources = [];

  foreach ( $manifest as $source => $one )
    if ( ! str_starts_with ( $source, '_' ) )
      $sources [] = [ 'source' => $source,
                      'file'   => $one ['file'],
                      'kind'   => ( $one ['isEntry'] ?? FALSE ) ? 'entry' : ( ( $one ['isDynamicEntry'] ?? FALSE ) ? 'loaded on demand' : 'chunk' ),
                      'css'    => implode ( ', ', $one ['css'] ?? [] ) ];

  $sourceCount = count ( $sources );
  $clock       = [ 'now' => time () ];

?>
