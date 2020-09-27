<?php

  if ( isset ( $pad_parms_pad ['toContentStore'] )  ) {
    $pad_content_store [$pad_parms_pad ['toContentStore']] = $pad_result [$pad_lvl];
  }

  if ( isset ( $pad_parms_pad ['toDataStore'] )  ) {
    $pad_data_store [$pad_parms_pad ['toDataStore']] = $pad_data [$pad_lvl];
  }

  if ( isset ( $pad_parms_pad ['toFlagStore'] )  ) {

    $pad_store_tmp = trim($pad_result [$pad_lvl]);

    if ( strlen($pad_store_tmp) )
      $pad_flag_store [$pad_parms_pad ['toFlagStore']] = TRUE;
    elseif ( $pad_true_false [$pad_lvl] )
      $pad_flag_store [$pad_parms_pad ['toFlagStore']] = TRUE;
    else
      $pad_flag_store [$pad_parms_pad ['toFlagStore']] = FALSE;

  }

  $pad_result [$pad_lvl] = '';
  
?>