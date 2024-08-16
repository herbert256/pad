<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;
  
  $title = "All 'ok' from regression test";

  foreach ( padList ( 0 ) as $one ) {

    $item   = $one ['item'];
    $store  = "/app/_regression/$item.txt";
    $status = padFileGetContents ($store, 'todo' );

    if ( $status == 'ok' and ! str_contains ($item, 'manual') )
      $list [$item] ['item'] = $item;
  
  }

  ksort ($list);

?>