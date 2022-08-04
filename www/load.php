 <?php

  $path = '/home/herbert/pad';

    $directory = new RecursiveDirectoryIterator ($path);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop) {

      $file = $loop->getPathname();

      if ( substr($file, -4) == '.php' )
        opcache_compile_file ($file);

    }


?>  