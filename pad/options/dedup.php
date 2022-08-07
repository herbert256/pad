<?php

  $pDedup = [];

  foreach ( $pData[$p] as $pK => $pV)
    if ( is_array ($pV) and count($pV) == 1 and isset ( $pV [$pName[$p]] ) )
      $pDedup [ $pV [$pName[$p]] ] = [ $pName[$p] => $pV [$pName[$p]] ] ;

  if ( count($pDedup))
    $pData[$p] = $pDedup;

?>