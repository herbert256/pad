<?php


  function recursiveDirs (  ) {

    $directory = new RecursiveDirectoryIterator ( padApp );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    $dirs = [];

    foreach ($iterator as $one)

     if ( $one->isDir() ) {

        $path = $one->getPathname() ;
        $dir  = str_replace ( padApp, '', $path );

        if ( substr($dir, -2) == '/.'  ) $dir = substr($dir, 0, -2);
        if ( substr($dir, -3) == '/..' ) $dir = substr($dir, 0, -3);

        if ( substr($dir, -1) <> '.' and count ( dirList ($path) ) )
          $dirs [$dir] ['dir'] = $dir;
   
      }

    ksort($dirs);

    return $dirs;

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

  
  function dirList ($dir) {

    if ( substr($dir, -2) == '..' ) return [];
    if ( ! is_dir($dir)           ) return [];

    $list = [];

    $directory = new DirectoryIterator ($dir);
    $iterator  = new IteratorIterator  ($directory);

    foreach ( $iterator as $loop_info ) {

      $one   = $loop_info->getPathname();
      $file  = str_replace($dir, '', $one );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 1, strrpos($file, '.')-1 );

      if ( substr($file, -1) == '.'         ) continue;
      if ( substr($file, -2) == '..'        ) continue;
      if ( is_dir($one)                     ) continue;
      if ( substr($item, -4) == 'todo'      ) continue;
      if ( substr($item, 0, 1) == '_'       ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      $list [$item] = $item;
      
    }

    sort($list);

    return $list;

  }

  function padPages ( ) {

    $files = [];

    $directory = new RecursiveDirectoryIterator (padApp);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $loop_info) {

      $file  = str_replace(padApp, '', $loop_info->getPathname() );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      if ( strpos($item, 'error') !== FALSE  ) continue;
      if ( strpos($item, 'todo')  !== FALSE  ) continue;
      if ( substr($item, 0, 1) == '_'        ) continue;
      if ( $ext <> 'html' and $ext <> 'php'  ) continue;
      if ( $item == 'development/ok'         ) continue;
      if ( $item == 'development/big'        ) continue;
      if ( $item == 'development/regression' ) continue;
      if ( $item == 'development/view'       ) continue;
      if ( in_array($item, $files)           ) continue;

      $files [$item] = $item;

    }

    ksort ($files);

    return $files;

  }

?>