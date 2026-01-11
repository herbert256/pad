<?php



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



  function padFileContains ( $file, $string ) {

    $file = str_replace ( '.php', '.pad', $file );

    if ( file_exists( $file ) )
      if ( str_contains ( padFileGet ( $file ), $string ) )
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