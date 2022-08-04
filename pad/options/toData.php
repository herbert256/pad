<?php

  $pad_store_name = $pad_prms_tag ['toData'];

  if ( !$pad_pair and !$pad_content and !pad_is_default_data($pad_data [$pad]) ) {
    $pad_data_store [$pad_store_name] = $pad_data [$pad];
    return;
  }

  if ( $pad_walks [$pad ] <> 'start' ) 
    $pad_data_store [$pad_store_name] = $pad_walks_data [ $pad];
  else
    $pad_data_store [$pad_store_name] = $pad_data [$pad];

  $pad_result [$pad] = '';
  
?>