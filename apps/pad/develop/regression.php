<?php

  $title = "Regression test";

  foreach ( padList ( 0 ) as $one ) {

    $item  = $one ['item'];
    $store = "/app/_regression/$item.txt";

    $list [$item] ['item']   = $item;
    $list [$item] ['status'] = padFileGetContents ($store, 'todo' );

  }

  ksort ($list);

?>