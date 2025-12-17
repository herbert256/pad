<?php


  function getPage ( $page, $ignoreErrors=0, $include=1 ) {

    global $padHost, $padScript;

    if ($include) $include = '&padInclude';
    else          $include = '';

    $url  = "$padHost$padScript?$page$include";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors and ! str_starts_with ( $curl ['result'], '2') )
      return padError ("Curl failed: $url");

    return $curl;

  }


  function dirList ($dir) {

    $list = [];

    $dir = APP . $dir;

    $directory = new DirectoryIterator ( $dir       );
    $iterator  = new IteratorIterator  ( $directory );

    foreach ( $iterator as $loop ) {

      if ( $loop->isDir() )
        continue;

      $one   = padCorrectPath ( $loop->getPathname() );
      $file  = str_replace($dir,  '', $one );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 1, strrpos($file, '.')-1 );

      if ( $item == 'index'                ) continue;
      if ( $item == 'all'                  ) continue;
      if ( $ext <> 'pad' and $ext <> 'php' ) continue;

      $list [$item] ['item'] = $item;

    }

    ksort($list);

    return $list;

  }


  function padList () {

    $directory = new RecursiveDirectoryIterator (APP);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path  = padCorrectPath ( $one->getPathname() );
      $file  = str_replace(APP, '', $path );
      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );

      if ( $ext <> 'pad'            ) continue;
      if ( strpos($path, '/DATA/')  ) continue;
      if ( strpos($path, '/_')      ) continue;
      if ( strpos($path, 'develop') ) continue;

      $files [$item] ['path'] = $path;
      $files [$item] ['item'] = $item;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;

    }

    ksort ($files);

    return $files;

  }


  function padItems ( $source ) {

    $directory = new RecursiveDirectoryIterator ( $source );
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path  = padCorrectPath ( $one->getPathname() );

      $file  = str_replace (APP, '', $path );
      $file  = str_replace (PAD, '', $path );

      $ext   = substr($file,    strrpos($file, '.')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );
      $dir   = substr($item, 0, strrpos($item, '/')   );
      $file  = substr($item,    strrpos($item, '/')+1 );

      if ( ! $item       ) continue;
      if ( $file == '.'  ) continue;
      if ( $file == '..' ) continue;
      if ( $ext  == 'md' ) continue;

      $files [$item] ['item'] = $item;
      $files [$item] ['path'] = $path;
      $files [$item] ['dir']  = $dir;
      $files [$item] ['file'] = $file;
      $files [$item] ['ext']  = $ext;

    }

    ksort ($files);

    return $files;

  }



  function padFileContains ( $file, $string ) {

    $file = str_replace ( '.php', '.pad', $file );

    if ( file_exists( $file ) )
      if ( str_contains ( fileGet ( $file ), $string ) )
        return TRUE;

    return FALSE;

  }


  function padListFiltered () {

    foreach ( padList () as $one ) {

      if ( str_contains ( $one ['path'], 'develop')     ) continue;
      if ( str_contains ( $one ['path'], 'manual')      ) continue;
      if ( str_contains ( $one ['path'], 'reference')   ) continue;

      if ( padFileContains ( $one ['path'], '{example') ) continue;
      if ( padFileContains ( $one ['path'], '{page')    ) continue;
      if ( padFileContains ( $one ['path'], '{ajax')    ) continue;

      $list [] = $one;

    }

    return $list;

  }




?>