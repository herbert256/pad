<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $title = "Regression test";

  foreach ( padPages () as $one ) {

    $item  = $one ['item'];
    $store = padApp . "_regression/$item.pad";
    $now   = padApp . "_regression/_now.txt";
    $check = padApp . "$item.pad";

    if ( strpos ( padFileGetContents($check), 'PAD: SKIP REGRESSION') )
      continue;

    $old = padFileGetContents($store);
    $new = getPageData ($item, 1);

    if     ( ! file_exists ($store) )               $status = 'new';
    elseif ( strrpos($store, 'random') )          $status = 'random' ;
    elseif ( strpos($new, 'PAD: NO REGRESSION') ) $status = 'skip' ;
    elseif ( $old == $new                       ) $status = 'ok';
    else                                          $status = 'error';

    if ( $status == 'new' ) {
      padFileChkDir     ( $store );
      padFileChkFile    ( $store );
      file_put_contents ( $store, $new, LOCK_EX ) ;
    }

    $files [$item] ['item']   = $item;
    $files [$item] ['status'] = $status;

  }
    
  ksort ($files);

?>