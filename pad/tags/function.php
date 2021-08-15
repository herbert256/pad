<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    $pad_walk = 'end';
    return TRUE;
  }

  $pad_tag = $pad_parm;

  if ( isset ($pad_parms_tag ['type'] ) )                     
    $pad_function_type = "function_" . $pad_parms_tag ['type'];                
  else
    $pad_function_type = "function_" . pad_function_type ($pad_parm);

  $pad_function_val = $pad_parms_val;
  unset ( $pad_function_val [ array_key_first ($pad_function_val) ] );

  return pad_function_in_tag ( $pad_function_type, $pad_parm, $pad_content, $pad_function_val );

?>