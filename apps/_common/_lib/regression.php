<?php


  function getRegression ( $extra='' ) {

    set_time_limit ( 60 );

    foreach ( padAppsList () as $one ) {

      extract ( $one );

      getRegressionGo ( $app, $item, $extra );

    }

  }


  function getRegressionGo ( $app, $item, $extra='' ) {

    global $padHost;

    $include = ( $item <> 'index' ) ? '&padInclude' : '';
    $store   = DAT . "regression/$app/$item.html";
 
    $curl   = padCurl    ( "$padHost/$app?$item$include$extra" );
    $source = padFileGet ( APPS . "$app/$item.pad" );
    $old    = padFileGet ( $store );

    $good = str_starts_with ( $curl ['result'], '2');
    $new  = $curl ['data'];

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

    if ( ! $extra <> '&padExamples' or ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;
 
    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    padFilePut ( "examples/$app/$item.pad",  padTidySmall ( $source,        TRUE ) );
    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

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