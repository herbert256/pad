<?php

  $pad_data [$pad] = pad_make_data ( $pad_data [$pad-1] [$pad_key[$pad-1]] );
    
  foreach ($pad_data [$pad-1] as $pad_k => $pad_v)
    if ( $pad_k <> [$pad_key[$pad-1]] )
      unset ( $pad_data [$pad-1] [$pad_k] );

  if ( count ($pad_data [$pad]) )
    return TRUE;
  else
    return FALSE;
  
?>