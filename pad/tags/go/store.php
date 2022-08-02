<?php

  if ( $pad_tag == $pad_name and $pad_parm and pad_valid ($pad_parm) and strlen($pad_parm) < 100)
    $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_parm;

  if ( $pad_tag == 'data' or $pad_tag == 'flag' or ($pad_walk == 'start' and $pad_parms_type == 'close') ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_store_name = 'pad_'.$pad_tag.'_store';

  if ( isset ( $pad_parms_val [1] ) ) {

    $pad_name         = $pad_parms_val [0];
    $pad_store_source = $pad_parms_val [1];
  
  } else {

    $pad_store_source = $pad_content;

  }

  if ( $pad_tag == 'content') {

    $pad_store_data = pad_make_content ($pad_store_source);
  
  } elseif ( $pad_tag == 'data' ) {

    if ( ! pad_is_default_data ( $pad_data[$pad_lvl] ) )
      $pad_store_data = $pad_data[$pad_lvl];
    elseif ( $pad_store_source )
      $pad_store_data = pad_make_data ($pad_store_source, pad_tag_parm('type'), $pad_name);
    else
      $pad_store_data = '';

  } elseif ( $pad_tag == 'flag' ) {

    if ( $pad_store_source )
      $pad_store_data = pad_make_flag ($pad_store_source);
    else
      $pad_store_data = $pad_true;

  }

  $GLOBALS [$pad_store_name] [$pad_name] = $pad_store_data;

  $pad_content = '';

?>