<?php

  set_include_path('');

  $padLibDirectory = new RecursiveDirectoryIterator ( pad . 'lib' );
  $padLibIterator  = new RecursiveIteratorIterator  ( $padLibDirectory );

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' )
      include_once $padLibFile;

  }

?>