<?php

  function getDocs ( $dir ) {

    $directory = new RecursiveDirectoryIterator ( padApp . $dir . '/docs' );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    $items = [];
    
    foreach ($iterator as $one) {

      $path  = padCorrectPath ( $one->getPathname() );
      $file  = str_replace(padApp, '', $path );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
 
      if ( $ext <> 'html' and $ext <> 'php' ) continue;
      if ( ! $item                          ) continue;
      if ( $file == '.'                     ) continue;
      if ( $file == '..'                    ) continue;
      if ( strpos ( $path, '.settings.' )   ) continue;
      if ( substr ( $item, 0, 1 ) == '_'    ) continue;

      $items [$item] ['item'] = $item;
 
    }
   
    ksort ( $items );
    return $items;

  }

?>