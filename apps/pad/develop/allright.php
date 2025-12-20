<?php

  $title = "All 'ok' from regression test";

  foreach ( padList () as $one ) {

    $item   = $one ['item'];
    $store  = APP . "_regression/$item.txt";
    $status = fileGet ($store, 'todo' );

    if ( $status == 'ok' and ! str_contains ($item, 'manual') )
      $list [$item] ['item'] = $item;

  }

  ksort ($list);

?>
