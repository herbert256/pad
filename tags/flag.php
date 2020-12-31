<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
 
    $pad_walk = 'end';

  } elseif ( $pad_walk == 'start' ) {
      
    $pad_walk = 'occurrence';

  } else {

    $pad_get = $pad_html [$pad_lvl];

    if ( strlen(trim($pad_html [$pad_lvl])) )
      $pad_flag_store [$pad_parm] = TRUE; 
    else
      $pad_flag_store [$pad_parm] = FALSE;

   $pad_html [$pad_lvl] = '';
   $pad_data [$pad_lvl] = [];

  }

  return TRUE;

?>