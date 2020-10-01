<?php

  $pad_dedup = [];

  foreach ( $pad_data [$pad_lvl] as $pad_k => $pad_v)
    if ( is_array ($pad_v) and count($pad_v) == 1 and isset ( $pad_v [$pad_name] ) )
      $pad_dedup [ $pad_v [$pad_name] ] = [ $pad_name => $pad_v [$pad_name] ] ;

  if ( count($pad_dedup))
    $pad_data [$pad_lvl] = $pad_dedup;

?>