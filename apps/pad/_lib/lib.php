<?php


  function getPage ( $page, $ignoreErrors=0 ) {

    global $padHost, $padScript;

    $url  = "$padHost$padScript?$page&padInclude";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors )
      if ( ! str_starts_with ( $curl ['result'], '2') )
        padError ("Curl failed: $url");

    return $curl;
    
  }


  function dirList ($dir) {

    $list = [];

    $dir = padApp . $dir;

    $directory = new DirectoryIterator ( $dir       );
    $iterator  = new IteratorIterator  ( $directory );

    foreach ( $iterator as $loop ) {

      if ( $loop->isDir() )
        continue;

      $one   = $loop->getPathname();
      $file  = str_replace($dir,  '', $one );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 1, strrpos($file, '.')-1 );

      if ( substr($item, -4) == 'todo'      ) continue;
      if ( $item == 'index'                 ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      $list [$item] ['item'] = $item;
      
    }

    ksort($list);

    return $list;

  }


  function recursivePages () {

    $directory = new RecursiveDirectoryIterator ( padApp );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    $pages = [];

    foreach ($iterator as $one) {

      $ext = $one->getExtension();

      if ( $ext == 'html' or $ext == 'php' ) { 

        $file = str_replace ( padApp, '', $one->getPathname() );
        $page = substr($file, 0, strrpos($file, '.')   );

        if ( substr ($page, 0, 1) <> '_' )
          $pages [$page] ['page'] = $page;

      }

    }

    ksort($pages);

    return $pages;

  }


  function padPages ( ) {

    $files = [];

    $directory = new RecursiveDirectoryIterator (padApp);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file  = str_replace(padApp, '', $loop_info->getPathname() );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      if ( strpos($item, 'error') !== FALSE       ) continue;
      if ( strpos($item, 'todo')  !== FALSE       ) continue;
      if ( strpos($item, 'test')  !== FALSE       ) continue;
      if ( strpos($item, '/_')    !== FALSE       ) continue;
      if ( strpos($file, 'development') !== FALSE ) continue;
      if ( substr($item, 0, 1) == '_'             ) continue;
      if ( $ext <> 'html' and $ext <> 'php'       ) continue;
      if ( $item == 'hello/html'                  ) continue;
         
      $files [$item] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }


?>