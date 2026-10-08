<?php

  // {vite 'src/main.js'} - the scripts and stylesheets of a Vite build, by the name of their
  // source (lib/vite.php): from the manifest of the build, or from the dev server while it
  // runs. Several entries: {vite 'src/main.js', 'src/admin.js'}; react writes the preamble
  // the React plugin needs for fast refresh when the dev server is asked.

  $padViteEntries = [];

  foreach ( array_slice ( $padOpt [$pad], 1 ) as $padViteOne )
    if ( is_string ( $padViteOne ) and trim ( $padViteOne ) !== '' )
      $padViteEntries [] = trim ( $padViteOne );

  if ( ! $padViteEntries ) {
    padError ( "{vite} names the entries of the build - {vite 'src/main.js'}" );
    return '';
  }

  return padViteTags ( $padViteEntries, (bool) padTagParm ( 'react', FALSE ) );

?>
