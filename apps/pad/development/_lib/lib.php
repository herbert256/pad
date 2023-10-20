<?php

  function padPages () {

    $directory = new RecursiveDirectoryIterator (padApp);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path  = padCorrectPath ( $one->getPathname() );
      $file  = str_replace(padApp, '', $path );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );
 
      if ( ! $dir                           ) continue;
      if ( strpos($path, 'error')           ) continue;
      if ( strpos($path, 'restart')         ) continue;
      if ( strpos($path, 'redirect')        ) continue;
      if ( strpos($path, '.settings.')      ) continue;
      if ( strpos($path, '/_')              ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;
      if ( $item == 'hello/html'            ) continue;
      if ( go ( $item )                     ) continue;

      $files [$item] ['path'] = $path;
      $files [$item] ['item'] = $item;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;
    
    }

    ksort ($files);

    return $files;

  }


  function padPagesFiltered () {

    $work = $result = padPages ();

    foreach ( $work as $one ) {

      if ( $one ['file'] == 'index' )
        foreach ($result as $key => $value )
          if ( $value ['dir'] == $one ['dir'] and $value ['file'] <> 'index')
            unset ( $result [$key] );

      if ( strpos ( file_get_contents ( $one ['path'] ), '<!-- PAD: NO ALL -->') ) 
        unset ( $result [$one['item']] );
 
    }

    return $result;

  }


?>