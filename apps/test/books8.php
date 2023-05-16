<?php 

    $source = '/Users/herbert/work/';
    $target = '/Users/herbert/books/_titles';

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

      $dir  = $target;
      $book = $dir . '/' . $item . '.epub';

      #echo "$path --> $book <br>";
      #continue;

      if ( ! file_exists ( $dir ) )
      	mkdir ( $dir );

      rename($path, $book); 


    }

 

?>