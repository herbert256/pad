<?php

  if ( $pad_walk == 'start' and $pad_prms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_tag = $pad_parm;

  if ( isset ($pad_prms_tag ['type'] ) )                     
    $pad_function_type = "function_" . $pad_prms_tag ['type'];                
  else
    $pad_function_type = "function_" . pad_function_type ($pad_parm);

  $pad_function_val = $pad_prms_val;
  unset ( $pad_function_val [ array_key_first ($pad_function_val) ] );

  return pad_function_in_tag ( $pad_function_type, $pad_parm, $pad_content, $pad_function_val );

?>