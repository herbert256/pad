<?php

  set_time_limit ( 15 );

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    if (   $sequence and ! str_contains ( $item, 'sequence' ) ) continue;
    if ( ! $sequence and ! str_contains ( $item, 'fragments') ) continue;

    $store  = "regression/DATA/$item.html";
    $check  = "$item.pad";

    $source = fileGet ( $check );

    filePutFile ( 'develop', 'regression.txt', $item ) ;

    if     ( ! $source                                  ) $status = 'no';
    elseif ( strpos ( $source, 'PAD: SKIP REGRESSION' ) ) $status = 'no';
    elseif ( strpos ( $store, 'error' )                 ) $status = 'no';
    elseif ( strpos ( $store, 'restart' )               ) $status = 'no';
    elseif ( strpos ( $store, 'redirect' )              ) $status = 'no';
    elseif ( strpos ( $store, 'deep' )                  ) $status = 'no';
    else {

      $old  = fileGet ($store);
      $curl = padCurl ( "$padHost$padScript?$item&padInclude&padReference" );
      $good = str_starts_with ( $curl ['result'], '2');
      $new  = $curl ['data'] ?? '';
      $new  = str_replace ( "\r\n", "\n", $new );

      if     ( ! $good                            ) $status = 'error';
      elseif ( ! file_exists ($store)             ) $status = 'new';
      elseif ( ! trim ($new)                      ) $status = 'empty';
      elseif ( strpos($source, '{example')        ) $status = 'skip' ;
      elseif ( strpos($source, '{ajax')           ) $status = 'skip' ;
      elseif ( strpos($source, 'random')          ) $status = 'random' ;
      elseif ( strpos($source, 'shuffle')         ) $status = 'random' ;
      elseif ( strpos($source, 'chance')          ) $status = 'random' ;
      elseif ( strpos($new, 'padAjax')            ) $status = 'skip' ;
      elseif ( $old == $new                       ) $status = 'ok';
      else                                          $status = 'warning';

      if ( $status == 'new' )
        filePutFile ( 'regression', "$item.html", $new ) ;

    }

    if ( $status == 'new' )
      $status = 'ok';

    filePutFile ( 'regression', "$item.txt", $status ) ;

  }

  if ( isset ($build) )
    return;

  include APP . 'regression/go.php';

?>