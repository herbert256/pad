<?php

  if ( isset ( $pad_parms_tag ['toContent'] )  ) {
    $pad_content_store [$pad_parms_tag ['toContent']] = $pad_result [$pad_lvl];
  }

  if ( isset ( $pad_parms_tag ['toData'] )  ) {
    if ( $pad_walks [$pad_lvl ]<> 'start' ) 
      $pad_data_store [$pad_parms_tag ['toData']] = $pad_walks_data [ $pad_lvl];
    else
      $pad_data_store [$pad_parms_tag ['toData']] = $pad_data [$pad_lvl];
    }

  if ( isset ( $pad_parms_tag ['toFlag'] )  ) {

    $pad_store_tmp = trim($pad_result [$pad_lvl]);

    if ( strlen($pad_store_tmp) )
      $pad_flag_store [$pad_parms_tag ['toFlag']] = TRUE;
    elseif ( $pad_true_false [$pad_lvl] )
      $pad_flag_store [$pad_parms_tag ['toFlag']] = TRUE;
    else
      $pad_flag_store [$pad_parms_tag ['toFlag']] = FALSE;

  }

  $pad_result [$pad_lvl] = '';
  
?>