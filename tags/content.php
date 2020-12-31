<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
 
    $pad_walk = 'end';

  } elseif ( $pad_walk == 'start' ) {
      
    $pad_walk = 'occurrence';

  } else {

    $pad_get = $pad_html [$pad_lvl];
    
    if ( $pad_tag == 'content')
      $pad_content_store [$pad_parm] = $pad_html [$pad_lvl]; 
    elseif ( $pad_tag == 'data')
      $pad_data_store [$pad_parm] = pad_data ( $pad_html [$pad_lvl], pad_tag_parm('type') );
    elseif ( strlen(trim($pad_html [$pad_lvl])) )
      $pad_flag_store [$pad_parm] = TRUE; 
    else
      $pad_flag_store [$pad_parm] = FALSE;

   $pad_html [$pad_lvl] = '';
   $pad_data [$pad_lvl] = [];

  }

  return TRUE;

?>