<?php

  set_time_limit ( 60 );

  foreach ( padAppsList () as $one ) {

    $item = $one ['item'];

    filePutFile ( 'develop', 'regression.txt', $item ) ;

    $store  = "regression/DATA/$item.html";
    $old    = fileGet ( $store );
    $source = fileGet ( "$item.pad" );
    $php    = fileGet ( "$item.php" );
    $curl   = padCurl ( "$padHost$padScript?$item&padInclude&padReference" );
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

    filePutFile ( 'regression', "$item.txt", $status ) ;

    if ( $status == 'new' )
      filePutFile ( 'regression', "$item.html", $new ) ;

    if ( ! $good or ! $new                    ) continue;
    if ( str_contains ( $source, '{page'    ) ) continue;
    if ( str_contains ( $source, '{example' ) ) continue;
    if ( str_contains ( $source, '{table'   ) ) continue;
    if ( str_contains ( $source, '{demo'    ) ) continue;
    if ( str_contains ( $source, '{ajax'    ) ) continue;

    if ( $php    ) filePutFile ( 'examples', "$item.php",  $php    ) ;
    if ( $source ) filePutFile ( 'examples', "$item.pad",  $source ) ;
    if ( $new    ) filePutFile ( 'examples', "$item.html", $new    ) ;

  }

  if ( isset ($build) )
    return;

  padRedirect ( "regression" );

?>