<?php

  if ( ! file_exists($pLib) )
    return;

  $pLib_directory = new RecursiveDirectoryIterator ($pLib);
  $pLib_iterator  = new RecursiveIteratorIterator  ($pLib_directory);

  foreach ( $pLib_iterator as $pLib_one ) {

    $pLib_file = $pLib_one->getPathname();

    if ( substr($pLib_file, -4) == '.php' )
      include_once $pLib_file;

  }

?>