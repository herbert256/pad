<?php

  if ( $pad_tag == $pad_name and $pad_parm and pad_valid ($pad_parm) and strlen($pad_parm) < 100)
    $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_parm;

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {

    if ( isset ( $pad_parms_tag ['occurence'] ) )
      $pad_walk = 'occurence-end';
    else
      $pad_walk = 'end';

    $pad_walks [$pad_lvl] = $pad_walk;

    return TRUE;

  }

  $pad_store_name = 'pad_'.$pad_tag.'_store';

  if ( isset ( $pad_parms_val [1] ) ) {

    $pad_store_entry  = $pad_parms_val [0];
    $pad_store_source = $pad_parms_val [1];
  
  } else {

    if     ( pad_tag_parm ('name')  ) $pad_store_entry = pad_tag_parm ('name');
    elseif ( pad_tag_parm ('store') ) $pad_store_entry = pad_tag_parm ('store');
    else                              $pad_store_entry = $pad_parm ;

    if   ( pad_tag_parm ($pad_tag)  )  $pad_store_source = pad_tag_parm ($pad_tag);
    else                               $pad_store_source = $pad_content;

  }

  if     ( $pad_tag == 'content') $pad_store_data = pad_make_content ($pad_store_source);
  elseif ( $pad_tag == 'data'   ) $pad_store_data = pad_make_data    ($pad_store_source, pad_tag_parm('type'), $pad_name);
  elseif ( $pad_tag == 'flag'   ) $pad_store_data = pad_make_flag    ($pad_store_source);

  $GLOBALS [$pad_store_name] [$pad_store_entry] = $pad_store_data;

  return NULL;

?>