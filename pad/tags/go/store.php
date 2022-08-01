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

  if ( isset ( $pad_parms_val [1] ) )
    $GLOBALS [ 'pad_' . $pad_tag . '_store' ] [$pad_parm] = $pad_parms_val [1];
  else
    $GLOBALS [ 'pad_' . $pad_tag . '_store' ] [$pad_parm] = $pad_content;

  return NULL;

?>