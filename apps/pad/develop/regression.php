<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  set_time_limit ( 300 );

  $title = "Regression test";

  foreach ( padList () as $one ) {

    $item   = $one ['item'];
    $store  = padApp . "_regression/$item.pad";
    $now    = padApp . "_regression/_now.txt";
    $check  = padApp . "$item.pad";
    $source = padFileGetContents($check);

    if ( strpos ( $source, 'PAD: SKIP REGRESSION') )
      continue;

    $old   = padFileGetContents($store);
    $curl  = getPage ($item ,1);
    $good  = str_starts_with ( $curl ['result'], '2');
    $new   = $curl ['data'] ?? '';

    if     ( ! $good                            ) $status = 'error';
    elseif ( ! file_exists ($store)             ) $status = 'new';
    elseif ( strrpos($store, 'random')          ) $status = 'random' ;
    elseif ( strpos($new, 'PAD: NO REGRESSION') ) $status = 'skip' ;
    elseif ( strpos($source, '{example')        ) $status = 'skip' ;
    elseif ( strpos($source, '{get')            ) $status = 'skip' ;
    elseif ( strpos($source, '{ajax')           ) $status = 'skip' ;
    elseif ( strpos($new, 'padAjax')            ) $status = 'skip' ;
    elseif ( $old == $new                       ) $status = 'ok';
    else                                          $status = 'error';

    if ( $status == 'new' ) {
      padFileChkDir     ( $store );
      padFileChkFile    ( $store );
      file_put_contents ( $store, $new, LOCK_EX ) ;
    }

    $list [$item] ['item']   = $item;
    $list [$item] ['status'] = $status;

  }
    
  ksort ($list);

?>