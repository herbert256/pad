<?php

  set_time_limit ( 60 );

  foreach ( padAppsList () as $one ) {

    extract ( $one );

    $store  = DAT . "regression/$app/$item.html";
    $old    = padFileGet ( $store );

    $source = padFileGet ( APPS . "$app/$item.pad" );
    $php    = padFileGet ( APPS . "$app/$item.php" );

    $curl   = padCurl ( "$padHost/$app?$item&padReference" );
    $good   = str_starts_with ( $curl ['result'], '2');
    $new    = $curl ['data'] ?? '';
    $new    = str_replace ( "\r\n", "\n", $new );

    if     ( ! $good                            ) $status = 'error';
    elseif ( ! file_exists ($store)             ) $status = 'new';
    elseif ( ! trim ($new)                      ) $status = 'empty';
    elseif ( strpos($source, 'random')          ) $status = 'random' ;
    elseif ( strpos($source, 'shuffle')         ) $status = 'random' ;
    elseif ( strpos($source, 'chance')          ) $status = 'random' ;
    elseif ( $old == $new                       ) $status = 'ok';
    else                                          $status = 'warning';

    padFilePut ( "regression/$app/$item.txt", $status ) ;

    if ( $status == 'new' )
      padFilePut ( "regression/$app/$item.html", $new ) ;

    if ( ! $good or ! $new                    ) continue;
    if ( str_contains ( $source, '{page'    ) ) continue;
    if ( str_contains ( $source, '{example' ) ) continue;
    if ( str_contains ( $source, '{table'   ) ) continue;
    if ( str_contains ( $source, '{demo'    ) ) continue;
    if ( str_contains ( $source, '{ajax'    ) ) continue;

    if ( $php    ) padFilePut ( "examples/$app/$item.php",  $php    ) ;
    if ( $source ) padFilePut ( "examples/$app/$item.pad",  $source ) ;
    if ( $new    ) padFilePut ( "examples/$app/$item.html", $new    ) ;

  }

?>