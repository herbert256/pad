<?php 

    $source = '/Users/herbert/Books/bewerkt/';
 
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

      $new = "/Users/herbert/Books/target/$name - $title.epub";

      echo "$path  ==>  $new<br>";

      rename($path, $new); 
  
    }

    exit;

?>