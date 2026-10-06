<?php

  // Every application, with the first sentence of its README's introduction and its roots.

  $apps = [];

  foreach ( editApps () as $name ) {

    $readme = (string) @file_get_contents ( APPS . "$name/README.md" );
    $about  = '';

    if ( preg_match ( '/## Introduction\s*\n+(.+?)(?=\n\s*\n|\n#|\z)/s', $readme, $m ) )
      $about = preg_replace ( '/\s+/', ' ', trim ( str_replace ( '`', '', $m [1] ) ) );

    if ( $name == '_common' and $about === '' )
      $about = 'Shared by every application that has _common on: tags, snippets, functions, the wrapper';

    if ( strlen ( $about ) > 160 )
      $about = substr ( $about, 0, 157 ) . '...';

    $apps [] = [ 'name' => $name, 'about' => $about, 'roots' => array_keys ( editRoots ( $name ) ) ];

  }

  return [ 'apps' => $apps, 'branch' => editGitBranch () ];

?>
