<?php

  function padAppsList () {

    $directory = new RecursiveDirectoryIterator (APPS);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      if ( strpos ( $path, '/_')      ) continue;
      if ( strpos ( $path, 'develop') ) continue;

      $ext = substr($path, strrpos($path, '.')+1 );

      if ( $ext <> 'pad' ) 
        continue;

      $file  = str_replace ( APPS, '', $path );

      $app   = substr($file, 0, strpos($file, '/')   );
      $file  = substr($file,    strpos($file, '/')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      $files [$item] ['path'] = $path;
      $files [$item] ['app']  = $app;
      $files [$item] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }
  
?>