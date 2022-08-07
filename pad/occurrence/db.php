<?php

  $pDb_lvl [$p] = [];

  for ( $pK = 1; $pK<=$pad; $pK++)
    if ( $pDb [$pK] )
      $pDb_lvl [$p] [$pDb [$pK]] = $pData [$pK] [$pKey [$pK]];

  pDb_get_info ();
  pDb_get_main ();
  
  foreach ( $pDb_lvl [$pad ]as $pK => $pad_v)
    if (  ! isset($GLOBALS [$pK] ) )
      pSet_global ( $pK, $pad_v );

  foreach ( $pDb_lvl [$p] as $pK => $pad_v)
    foreach ( $pad_v as $pK2 => $pad_v2)
      if (  ! isset($GLOBALS [$pK2] ) )
        pSet_global ( $pK2, $pad_v2 );

?>