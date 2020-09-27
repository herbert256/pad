<?php

  if ( $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  if ( strlen(trim($pad_content)) )
    $pad_flag_store [$pad_name] = TRUE; 
  elseif ( count ($pad_data [$pad_lvl]) > 1 )
    $pad_flag_store [$pad_name] = TRUE; 
  elseif ( count ( $pad_data [$pad_lvl] ) and ! isset ( $pad_data [$pad_lvl][1] )   )
    $pad_flag_store [$pad_name] = TRUE; 
  elseif ( isset ( $pad_data [$pad_lvl][1] )  and count (isset ( $pad_data [$pad_lvl][1] )  )
    $pad_flag_store [$pad_name] = TRUE; 
  else
    $pad_flag_store [$pad_name] = FALSE;

  $pad_content = '';
  
  return '';

?>