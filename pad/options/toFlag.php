<?php

  $pad_store_name = $pad_prms_tag ['toFlag'];

  if     ( $pad_parms [$pad] ['null']   ) $pad_flag_store [$pad_store_name] = FALSE;
  elseif ( $pad_parms [$pad] ['else']   ) $pad_flag_store [$pad_store_name] = FALSE;
  elseif ( trim ( $pad_result [$pad] ) <> '' ) $pad_flag_store [$pad_store_name] = TRUE;
  else                                             $pad_flag_store [$pad_store_name] = FALSE;

  $pad_result [$pad] = '';
  
?>