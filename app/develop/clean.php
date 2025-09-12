<?php

  trimFiles ( PAD );
  trimFiles ( APP );

  echo 'done';

  function trimFiles ( $path ) {

    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file = padCorrectPath ( $loop_info->getPathname() );
      $ext  = substr($file, strrpos($file, '.')+1 );

      if ( $ext <> 'php' and $ext <> 'pad' ) 
        continue;

      $old = file_get_contents($file);
      $new = trim($old);

      if ($old <> $new) 
        file_put_contents ($file, $new, LOCK_EX);

    }

  }

?>