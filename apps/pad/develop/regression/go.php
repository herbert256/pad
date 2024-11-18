<?php

  set_time_limit ( 300 );

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    $store  = APP . "_regression/$item.html";
    $check  = APP . "$item.pad";
    $source = padFileGetContents($check);

    if     ( strpos ( $source, 'PAD: SKIP REGRESSION' ) ) $status = 'no';
    elseif ( strpos ( $store, 'error' )                 ) $status = 'no';
    elseif ( strpos ( $store, 'test' )                  ) $status = 'no';
    elseif ( strpos ( $store, 'restart' )               ) $status = 'no';
    elseif ( strpos ( $store, 'redirect' )              ) $status = 'no';
    elseif ( strpos ( $store, 'deep' )                  ) $status = 'no';
    else                                                  $status = 'go';

    if ( $status == 'go' ) {

      $old   = padFileGetContents($store);
      $curl  = getPage ($item ,1);
      $good  = str_starts_with ( $curl ['result'], '2');
      $new   = $curl ['data'] ?? '';

      if     ( ! $good                            ) $status = 'error';
      elseif ( ! file_exists ($store)             ) $status = 'new';
      elseif ( strrpos($store, 'random')          ) $status = 'random' ;
      elseif ( strpos($new, 'PAD: NO REGRESSION') ) $status = 'skip' ;
      elseif ( strpos($source, '{example')        ) $status = 'skip' ;
      elseif ( strpos($source, '{ajax')           ) $status = 'skip' ;
      elseif ( strpos($source, 'random')          ) $status = 'random' ;
      elseif ( strpos($source, 'shuffle')         ) $status = 'random' ;
      elseif ( strpos($new, 'padAjax')            ) $status = 'skip' ;
      elseif ( $old == $new                       ) $status = 'ok';
      else                                          $status = 'warning';

      if ( $status == 'new' ) {
        padFileChkDir     ( $store );
        padFileChkFile    ( $store );
        file_put_contents ( $store, $new ) ;
      }

    }

    if ( $status == 'new' )
      $status = 'ok';

    $store = APP . "_regression/$item.txt";
    padFileChkDir     ( $store );
    padFileChkFile    ( $store );
    file_put_contents ( $store, $status ) ;

  }

  padRedirect ( 'develop/regression' );
    
?>