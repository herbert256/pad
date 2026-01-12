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


?>