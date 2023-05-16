<?php 

    $source = '/Users/herbert/Books/';
 
    $files = [];

    $directory = new RecursiveDirectoryIterator ($source);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop) {

      $path  = $loop->getPathname();
      $file  = str_replace($source, '', $path);
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      if ( $ext <> 'epub' ) 
        continue;
       
      $parts1 = padExplode ( $item, '/');

      if ( count ( $parts1) <> 2 )
        continue;

      $name  = $parts1 [0];
      $title = $parts1 [1];

      $names  [$name]  [$title] = TRUE;
      $titles [$title] [$name] = TRUE;

    }

      foreach ($titles as $key => $value)
        if ( count ($value) > 1 )
          if ( file_exists ( "/Users/herbert/Books/_titles/$key.epub") )
            unlink ("/Users/herbert/Books/_titles/$key.epub");

      foreach ($titles as $key => $value)
        if ( count ($value) > 1 )
          $dups [$key] = $value;


    dump();

?>