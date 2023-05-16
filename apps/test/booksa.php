<?php 

    $source = '/Users/herbert/Books/dups/';
 
    $files = [];

    $directory = new RecursiveDirectoryIterator ($source);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop) {

      $path = $loop->getPathname();

      if ( strpos($path, ' - ') === FALSE)
        continue;

      $file  = str_replace($source, '', $path);
      $name  = substr($file, 0, strrpos($file, '/')   );
      $title = substr($file, strrpos($file, '/') + 1  );
      $new   = "/Users/herbert/Books/target/$title.epub";

      rename($path, $new); 
      
      echo "$path  ==>  $new<br>";

    }

exit;

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