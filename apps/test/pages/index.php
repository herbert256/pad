<?php

  $padLibDirectory = new RecursiveDirectoryIterator ('/home/herbert/pad');
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' ) {

      $data = file_get_contents( $padLibFile);

      file_put_contents( $padLibFile, trim($data) );

    }

  }

  dump();
 
?>