<?php

  $pad_db_lvl [$pad] = [];

  for ( $pad_k = 1; $pad_k<=$pad; $pad_k++)
    if ( $pad_db [$pad_k] )
      $pad_db_lvl [$pad] [$pad_db [$pad_k]] = $pad_data [$pad_k] [$pad_key [$pad_k]];

  pad_db_get_info ();
  pad_db_get_main ();
  
  foreach ( $pad_db_lvl [$pad ]as $pad_k => $pad_v)
    if (  ! isset($GLOBALS [$pad_k] ) )
      pad_set_global ( $pad_k, $pad_v );

  foreach ( $pad_db_lvl [$pad] as $pad_k => $pad_v)
    foreach ( $pad_v as $pad_k2 => $pad_v2)
      if (  ! isset($GLOBALS [$pad_k2] ) )
        pad_set_global ( $pad_k2, $pad_v2 );

?>