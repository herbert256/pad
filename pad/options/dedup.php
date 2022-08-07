<?php

  $pDedup = [];

  foreach ( $pData[$p] as $pK => $pad_v)
    if ( is_array ($pad_v) and count($pad_v) == 1 and isset ( $pad_v [$pName[$p]] ) )
      $pDedup [ $pad_v [$pName[$p]] ] = [ $pName[$p] => $pad_v [$pName[$p]] ] ;

  if ( count($pDedup))
    $pData[$p] = $pDedup;

?>