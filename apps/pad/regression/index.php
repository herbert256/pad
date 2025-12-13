<?php

  $title = "Regression test";

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    if (   $sequence and ! str_contains ( $item, 'sequence'  ) ) continue;
    if ( ! $sequence and ! str_contains ( $item, 'fragments' ) ) continue;

    $list [$item] ['item']   = $item;
    $list [$item] ['status'] = fileGet ( 'regression/DATA/' . "$item.txt" );

  }

  ksort ($list);

?>