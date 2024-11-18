<?php

    $base = $padOpt [$pad] [1];

    $list = glob ( "$base/*" );

    $items = [];

    foreach ( $list as $item ) {
      $items [$item] ['base'] = $base;
      $items [$item] ['item'] = str_replace ( "$base/", '', str_replace ('.txt', '', $item) );
    }

    return $items;

?>