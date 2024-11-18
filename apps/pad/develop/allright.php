<?php
  
  $title = "All 'ok' from regression test";

  foreach ( padList ( 0 ) as $one ) {

    $item   = $one ['item'];
    $store  = APP . "_regression/$item.txt";
    $status = padFileGetContents ($store, 'todo' );

    if ( $status == 'ok' and ! str_contains ($item, 'manual') )
      $list [$item] ['item'] = $item;
  
  }

  ksort ($list);

?>