<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $title = "Regression test";

  foreach ( padPages () as $one ) {

    $item = $one ['item'];

    $curl  = getPage ($item, 1);
    $store = padApp . "_regression/$item.html";
  
    if     ( $curl ['result'] <> 200 )                              $status = 'error' ;
    elseif ( ! padExists ($store) )                                 $status = 'new';
    elseif ( strrpos($store, 'random') )                            $status = 'random' ;
    elseif ( strrpos($store, 'test') )                              $status = 'test' ;
    elseif ( strpos($curl['data'], '<!-- PAD: NO REGRESSION -->') ) $status = 'skip' ;
    elseif ( padFileGetContents($store) == $curl ['data']         ) $status = 'ok';
    else                                                            $status = 'error';

    if ( $status == 'new' ) {
      padFileChkDir     ( $store );
      padFileChkFile    ( $store );
      file_put_contents ( $store, $curl ['data'], LOCK_EX ) ;
    }

    $files [$item] ['item']  = $item;
    $files [$item] ['status'] = $status;

  }
    
  ksort ($files);

?>