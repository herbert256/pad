<?php

  if ( ! file_exists($padLib) )
    return;

  $padLib_directory = new RecursiveDirectoryIterator ($padLib);
  $padLib_iterator  = new RecursiveIteratorIterator  ($padLib_directory);

  foreach ( $padLib_iterator as $padLib_one ) {

    $padLib_file = $padLib_one->getPathname();

    if ( substr($padLib_file, -4) == '.php' )
      include_once $padLib_file;

  }

?>