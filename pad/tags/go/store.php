<?php

  if ( $pad_tag == $pad_name and $pad_parm and pad_valid ($pad_parm) and strlen($pad_parm) < 100) {
    $pad_name = $pad_parm;
    $pad_parameters [$pad_lvl] ['name'] = $pad_parm;
  }

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {

    if ( isset ( $pad_parms_tag ['occurence'] ) )
      $pad_walk = 'occurence-end';
    else
      $pad_walk = 'end';

    $pad_walks [$pad_lvl] = $pad_walk;

    return TRUE;

  }

  if ( isset ( $pad_parms_val [1] ) ) {

    $pad_get_data = $pad_parms_val [1];
    $pad_get_prm  = $pad_parms_val;

    unset ( $pad_get_prm [0] );
    unset ( $pad_get_prm [1] );

  } else  {

    $pad_get_data = $pad_content;
    $pad_get_prm  = [];

  }

  $pad_get_store    = "pad_$pad_tag" . "_store";
  $pad_get_function = "pad_get_$pad_tag";

  $GLOBALS [$pad_get_store] [$pad_parm] = $pad_get_function ( $pad_get_data, pad_tag_parm('type'), $pad_get_prm ); 

  return NULL;

?>