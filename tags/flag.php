<?php

  if ( $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  if ( strlen(trim($pad_content)) )
    $pad_flag_store [$pad_parm] = TRUE; 
  elseif ( count ($pad_data [$pad_lvl]) > 1 )
    $pad_flag_store [$pad_parm] = TRUE; 
  elseif ( count ( $pad_data [$pad_lvl] ) and ! isset ( $pad_data [$pad_lvl][1] )   )
    $pad_flag_store [$pad_parm] = TRUE; 
  elseif ( isset ( $pad_data [$pad_lvl][1] ) and count ( $pad_data [$pad_lvl][1] )  )
    $pad_flag_store [$pad_parm] = TRUE; 
  else
    $pad_flag_store [$pad_parm] = FALSE;

  $pad_content = '';
  
  return '';

?>