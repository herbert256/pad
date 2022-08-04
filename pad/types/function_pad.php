<?php

  if ( $pad_walk == 'start' and $pad_prms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }
   
  return pad_function_in_tag ( $pad_type, $pad_tag, $pad_content, $pad_prms_val );

?>