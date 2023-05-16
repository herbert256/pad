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

      $names  [$name]  [$title] = $path;
      $titles [$title] [$name] = $path;

    }

    foreach ($titles as $key => $value)
      if ( count ($value) > 1 )
        $dups [$key] = $value;

    foreach ($dups as $title => $value)
      foreach ($value as $name => $old) {
        $dir = "/Users/herbert/Books/dups/$title";
        $new = $dir . "/$name - $title";
        if ( ! file_exists ( $dir ) )
          mkdir ( $dir );
        rename($old, $new); 
        echo "$old  ==>  $new<br>";
      }

?>