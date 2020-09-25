<?php

  if ( $pad_single and ! isset ( $pad_parms_seq [1] ) )
    return pad_tag_error ();
  
  if ( $pad_parms_type == 'close' and $pad_walk == 'start' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  if ( $pad_single )
    $pad_content = pad_external ( $pad_parms_seq [1] );

  include PAD_HOME . 'level/after.php';

  $pad_content_store [$pad_parm] = $pad_content;
  $pad_content = '';

  return NULL;

?>