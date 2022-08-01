<?php

  if ( ! file_exists($pad_lib) )
    return;

  $pad_lib_directory = new RecursiveDirectoryIterator ($pad_lib);
  $pad_lib_iterator  = new RecursiveIteratorIterator  ($pad_lib_directory);

  foreach ( $pad_lib_iterator as $pad_lib_one ) {

    $pad_lib_file = $pad_lib_one->getPathname();

    if ( substr($pad_lib_file, -4) == '.php' )
      include_once $pad_lib_file;

  }

?>