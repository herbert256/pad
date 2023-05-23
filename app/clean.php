<?php

  trimFiles ( pad    );
  trimFiles ( padApp );

  echo 'done';

  function trimFiles ( $path ) {

    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file = $loop_info->getPathname() ;
      $ext  = substr($file, strrpos($file, '.')+1 );

      if ( $ext <> 'php' and $ext <> 'html' ) 
        continue;

      $old = file_get_contents($file);
      $new = trim($old);

      if ($old <> $new) 
        file_put_contents ($file, $new, LOCK_EX);

    }

  }

?>