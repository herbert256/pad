<?php

  $showTitle = FALSE;
  
  function dirsExample () {

    $directory = new RecursiveDirectoryIterator ( padApp );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    $dirs = [];

    foreach ($iterator as $one)

     if ( $one->isDir() ) {

        $path = $one->getPathname() ;
        $dir  = str_replace ( padApp, '', $path );

        if ( strpos($path, '/_includes/') !== FALSE)
          continue;

        if ( substr($dir, -2) == '/.'  ) $dir = substr($dir, 0, -2);
        if ( substr($dir, -3) == '/..' ) $dir = substr($dir, 0, -3);

        if ( ! isset ($dirs [$dir]) and substr($dir, -1) <> '.' and substr($dir, 0, 1) <> '_' ) {
          $dirs [$dir] ['dir']   = $dir;
          $dirs [$dir] ['files'] = dirExample ($dir);
        } 

      }

    ksort($dirs);

    return $dirs;

  }

  function dirExample ($dir) {

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
  
      if ( $item == 'index' )
        return [ 'file' => ' index', 'onlyResult' => ',onlyResult', 'skipResult' => '' ]; 

      if ( isset ($list [$item] )           ) continue;
      if ( substr($item, -4) == 'todo'      ) continue;
      if ( substr($item, 0, 1) == '_'       ) continue;
      if ( $ext <> 'html' and $ext <> 'php' ) continue;

      $html = ( padExists("$dir/$item.html") ) ? file_get_contents("$dir/$item.html") : '';

      if ( strpos($html, '{restart')  !== false ) continue;  
      if ( strpos($html, '{redirect') !== false ) continue;  

      $list [$item] ['file'] = $item;

    }

    ksort($list);

    return $list;

  } 

?>