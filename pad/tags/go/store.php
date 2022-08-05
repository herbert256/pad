<?php

  if ( $pad_walk == 'start' and ($pad_tag == 'data' or $pad_tag == 'flag' or $pad_prms_type == 'close' ) ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_store_name = 'pad_'.$pad_tag.'_store';

  if ( isset ( $pad_prms_val [0] ) or isset ( $pad_prms_val [1] ) ) 
    $pad_name = $pad_prms_val [0];

  if ( isset ( $pad_prms_val [1] ) )
    $pad_store_source = $pad_prms_val [1];  
  else
    $pad_store_source = $pad_content;

  $pad_parms [$pad] ['name'] = $pad_name;

  if ( $pad_tag == 'content') {

    $pad_store_data = pad_make_content ($pad_store_source);
  
  } elseif ( $pad_tag == 'data' ) {

    if ( ! pad_is_default_data ( $pad_data[$pad] ) )
      $pad_store_data = $pad_data[$pad];
    elseif ( $pad_store_source )
      $pad_store_data = pad_make_data ($pad_store_source, pad_tag_parm('type'), $pad_name);
    else
      $pad_store_data = '';

  } elseif ( $pad_tag == 'flag' ) {

    if ( $pad_store_source )
      $pad_store_data = pad_make_flag ($pad_store_source);
    else
      $pad_store_data = $pad_hit;

  }

  $GLOBALS [$pad_store_name] [$pad_name] = $pad_store_data;

  $pad_content = '';

?>