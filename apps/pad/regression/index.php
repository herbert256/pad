<?php

  $title = "Regression test";

  foreach ( padList () as $one ) {

    $item = $one ['item'];

    $list [$item] ['item']   = $item;
    $list [$item] ['status'] = fileGet ( 'regression/DATA/' . "$item.txt" );

  }

  ksort ($list);

?>