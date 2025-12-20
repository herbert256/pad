<?php

  $padLibDirectory = new RecursiveDirectoryIterator ( PAD . 'lib' );
  $padLibIterator  = new RecursiveIteratorIterator  ( $padLibDirectory );

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = str_replace ('\\', '/' , $padLibOne->getPathname() );

    if ( substr($padLibFile, -4) == '.php' )
      include_once $padLibFile;

  }

?>
