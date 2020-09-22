<?php

  if ( $pad_pair and $pad_parms_type == 'close' and $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_data [$pad_lvl] = [];

  if ( $pad_single and ! isset ( $pad_parms_seq [1] ) )
    return pad_tag_error ();

  if ( $pad_single )
    $pad_data_parm = trim($pad_parms_seq [1]);
  else
    $pad_data_parm = '';

  $pad_data_stream = pad_explode ($pad_data_parm, '://');
  if ( count($pad_data_stream) == 2)
    $pad_data_protocol = $pad_data_stream[0];
  else
    $pad_data_protocol = '';

  if ( ! ( $pad_single and pad_tag_parm('after') and $pad_walk == 'end') ) {

    if ( $pad_single and $pad_data_protocol )
      $pad_content = pad_external ($pad_parms_seq [1]);

    if ( $pad_single and !$pad_data_protocol and ctype_digit($pad_data_parm) )
      $pad_data [$pad_lvl] = range ( 1, intval($pad_data_parm, 10) );

    $pad_data_range = pad_explode ($pad_data_parm, '..');
    if ( $pad_single and !$pad_data_protocol and count ($pad_data_range) == 2 )
        $pad_data [$pad_lvl] = range ( $range[0], $range[1] );

  }
 
  if ( $pad_content and count ( $pad_data [$pad_lvl] ) )
    return pad_tag_error ("Both content and data");

  if ( $pad_single and pad_tag_parm('after') and $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;      
  }

  if ( $pad_content )
    $pad_data [$pad_lvl] = pad_data ( $pad_content, pad_tag_parm ('type') );    

  include PAD_HOME . 'level/after.php';

  $pad_data_store [$pad_parm] = $pad_data [$pad_lvl]; 

  $pad_data[$pad_lvl] = [];
  $pad_content        = '';

  return NULL;

?>