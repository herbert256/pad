<?php


  function getRegression ( $filter=0 ) {

    set_time_limit ( 60 );

    foreach ( padAppsList () as $one ) {

      extract ( $one );

      if ( $filter == 1 and $app <> 'sequence' ) continue;
      if ( $filter == 2 and $app == 'sequence' ) continue;

      getRegressionGo ( $app, $item );

    }

  }


  function getRegressionGo ( $app, $item ) {

    global $padHost;

    $include = ( $item <> 'index' ) ? '&padInclude' : '';
    $store   = DAT . "regression/$app/$item.html";
 
    $curl   = padCurl    ( "$padHost/$app?$item$include" );
    $source = padFileGet ( APPS . "$app/$item.pad" );
    $old    = padFileGet ( $store );

    $good = str_starts_with ( $curl ['result'], '2');
    $new  = str_replace     ( "\r\n", "\n", $curl ['data'] );

    if     ( ! $good                    ) $status = 'error';
    elseif ( ! file_exists ($store)     ) $status = 'new';
    elseif ( ! trim ($new)              ) $status = 'empty';
    elseif ( strpos($source, 'random')  ) $status = 'random' ;
    elseif ( strpos($source, 'shuffle') ) $status = 'random' ;
    elseif ( strpos($source, 'chance')  ) $status = 'random' ;
    elseif ( $old == $new               ) $status = 'ok';
    else                                  $status = 'warning';

    if ( $status == 'new' )
      padFilePut ( $store, $new ) ;

    padFilePut ( str_replace ( '.html', '.txt', $store ), $status ) ;

  }


  function padAppsList () {

    $directory = new RecursiveDirectoryIterator (APPS);
    $iterator  = new RecursiveIteratorIterator  ($directory);

    foreach ($iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname() );

      if ( strpos ( $path, '/_')      ) continue;
      if ( strpos ( $path, 'develop') ) continue;

      $ext = substr($path, strrpos($path, '.')+1 );

      if ( $ext <> 'pad' ) 
        continue;

      $file  = str_replace ( APPS, '', $path );

      $app   = substr($file, 0, strpos($file, '/')   );
      $file  = substr($file,    strpos($file, '/')+1 );
      $item  = substr($file, 0, strrpos($file, '.')   );

      $files [$item] ['path'] = $path;
      $files [$item] ['app']  = $app;
      $files [$item] ['item'] = $item;

    }

    ksort ($files);

    return $files;

  }

?>