<?php

  $pad_store_tmp = trim ($pad_result [$pad_lvl]);

  if ( strlen($pad_store_tmp) )
    $pad_flag_store [$pad_parms_tag ['toFlag']] = TRUE;
  elseif ( $pad_true_false [$pad_lvl] )
    $pad_flag_store [$pad_parms_tag ['toFlag']] = TRUE;
  else
    $pad_flag_store [$pad_parms_tag ['toFlag']] = FALSE;

  $pad_result [$pad_lvl] = '';
  
?>