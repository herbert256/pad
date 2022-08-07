<?php

  $pDedup = [];

  foreach ( $pData [$pad] as $pK => $pad_v)
    if ( is_array ($pad_v) and count($pad_v) == 1 and isset ( $pad_v [$pName] ) )
      $pDedup [ $pad_v [$pName] ] = [ $pName => $pad_v [$pName] ] ;

  if ( count($pDedup))
    $pData [$pad] = $pDedup;

?>