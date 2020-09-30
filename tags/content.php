<?php

  if ( $pad_pair and $pad_parms_type == 'close' and $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  if ( $pad_walk == 'start' ) {
    $pad_walk = 'occurrence/start';
    return TRUE;
  } 

  if ( isset($pad_parms_seq[1]) )
    if ( $pad_tag == 'content')
      $pad_content .= pad_content ( $pad_parms_seq[1] ); 
    else
      pad_add_array_to_data ( $pad_parms_seq[1], pad_tag_parm('type') ); 
 
  if ( $pad_tag == 'content')
    $pad_content_store [$pad_parm] = $pad_content; 
  else {
    pad_add_array_to_data ( $pad_content, pad_tag_parm('type') ); 
    $pad_data_store [$pad_parm] = $pad_data [$pad_lvl]; 
  }

  $pad_html [$pad_lvl] = '';
  $pad_data [$pad_lvl] = [];

  $pad_content = '';
  $pad_walk    = '';

  return TRUE;

?>