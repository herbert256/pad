<?php

  trimPhp ( pad    );
  trimPhp ( padApp );

  echo 'done';

  function trimPhp ( $path ) {

    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file = $loop_info->getPathname() ;
      $ext  = substr($file, strrpos($file, '.')+1 );

      if ( $ext <> 'php' ) 
        continue;

      $old = file_get_contents($file);
      $new = trim($old);

      if ($old <> $new) 
        file_put_contents ($file, $new, LOCK_EX);

    }

  }

?>