<?php

  $pad_data [$pad_lvl] = pad_make_data ( $pad_data [$pad_lvl-1] [$pad_key[$pad_lvl-1]] );
    
  foreach ($pad_data [$pad_lvl-1] as $pad_k => $pad_v)
    if ( $pad_k <> [$pad_key[$pad_lvl-1]] )
      unset ( $pad_data [$pad_lvl-1] [$pad_k] );

  if ( count ($pad_data [$pad_lvl]) )
    return TRUE;
  else
    return FALSE;
  
?>