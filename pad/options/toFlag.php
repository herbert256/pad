<?php

  $pad_store_name = $pad_parms_tag ['toFlag'];

  if     ( $pad_parameters [$pad_lvl] ['null'] ) ) $pad_flag_store [$pad_store_name] = FALSE;
  elseif ( $pad_parameters [$pad_lvl] ['else'] ) ) $pad_flag_store [$pad_store_name] = FALSE;
  elseif ( trim ( $pad_result [$pad_lvl] ) <> '' ) $pad_flag_store [$pad_store_name] = TRUE;
  else                                             $pad_flag_store [$pad_store_name] = FALSE;

  $pad_result [$pad_lvl] = '';
  
?>