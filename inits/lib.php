<?php

  if ( ! file_exists($padLib) )
    return;

  $padLibDirectory = new RecursiveDirectoryIterator ($padLib);
  $padLibIterator  = new RecursiveIteratorIterator  ($padLibDirectory);

  foreach ( $padLibIterator as $padLibOne ) {

    $padLibFile = $padLibOne->getPathname();

    if ( substr($padLibFile, -4) == '.php' )
      include_once $padLibFile;

  }

?>