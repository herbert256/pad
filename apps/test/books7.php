<?php 

    $source = '/Users/herbert/work/';
    $target = '/Users/herbert/books/';

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
       
      $parts1 = padExplode ( $item, '-', 2);

      if ( count ( $parts1) <> 2 )
      	continue;

      $name  = str_replace('.', '' , $parts1 [0]);
      $title = $parts1 [1];

      $dir = $target . $name;
      $book = $dir . '/' . $title . '.epub';

      #echo "$path --> $book <br>";
      #continue;

      if ( ! file_exists ( $dir ) )
      	mkdir ( $dir );

      rename($path, $book); 

    }

 

?>