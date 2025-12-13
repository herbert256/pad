<?php

  trimFiles ( PAD );

  trimFiles (  '/home/herbert/apps/' );

  echo 'done';

  function trimFiles ( $path ) {

    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file = padCorrectPath ( $loop_info->getPathname() );
      $ext  = substr($file, strrpos($file, '.')+1 );

      if ( $ext <> 'php' and $ext <> 'pad' ) 
        continue;

      $old = fileGet ($file);
      $new = trim($old);

      if ($old <> $new) 
        filePutFile ($file, $new, LOCK_EX);

    }

  }

?>