<?php

  if ( ! isset($pad_parms_val[0]) )
    return pad_tag_error ();
  
  $pad_if   = $pad_parms_org[0];
  $pad_else = strpos($pad_content, '{elseif');

  while ($pad_else !== FALSE) {

    if ( ! pad_check_tag ('if', substr($pad_content, 0, $pad_else)) )

      $pad_else = strpos($pad_content , '{elseif', $pad_else+7);

    else {

      if ( pad_eval ($pad_if) ) {
        pad_trace ('if', 'TRUE: ' . $pad_if);
        $pad_content = substr ($pad_content, 0, $pad_else);
        return TRUE;
      }

      pad_trace ('if', 'FALSE: ' . $pad_if);

      $pad_pos     = strpos ( $pad_content, '}', $pad_else );
      $pad_if      = substr ( $pad_content, $pad_else+8, $pad_pos-($pad_else+8) );
      $pad_content = substr ( $pad_content, $pad_pos+1 );
      $pad_else    = strpos ( $pad_content, '{elseif' );

    }

  }

  if ( pad_eval ($pad_if) ) {
    pad_trace ('if', 'TRUE: ' . $pad_if);
    return TRUE;
  }
  
  pad_trace ('if', 'FALSE: ' . $pad_if);
  return FALSE;

?>