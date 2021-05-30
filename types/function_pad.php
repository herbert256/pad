<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }
   
  return pad_function_in_tag ( $pad_tag_type, $pad_tag, $pad_content, $pad_parms_val );

?>