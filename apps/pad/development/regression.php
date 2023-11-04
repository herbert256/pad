<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $title = "Regression test";

  foreach ( padPages () as $one ) {

    $item  = $one ['item'];
    $store = padApp . "_regression/$item.pad";
    $now   = padApp . "_regression/_now.txt";
    $check = padApp . "$item.pad";

    if ( strpos ( padFileGetContents($check), '<!-- PAD: SKIP REGRESSION -->') )
      continue;

    file_put_contents ($now, $item, LOCK_EX);

    $curl  = getPage ($item, 1);

    if     ( $curl ['result'] <> 200 )                              $status = 'error' ;
    elseif ( ! padExists ($store) )                                 $status = 'new';
    elseif ( strrpos($store, 'random') )                            $status = 'random' ;
    elseif ( strpos($curl['data'], '<!-- PAD: NO REGRESSION -->') ) $status = 'skip' ;
    elseif ( padFileGetContents($store) == $curl ['data']         ) $status = 'ok';
    else                                                            $status = 'error';

    if ( $status == 'new' ) {
      padFileChkDir     ( $store );
      padFileChkFile    ( $store );
      file_put_contents ( $store, $curl ['data'], LOCK_EX ) ;
    }

    $files [$item] ['item']   = $item;
    $files [$item] ['status'] = $status;

  }
    
  ksort ($files);

?>