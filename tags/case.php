<?php
  
  if ( ! isset($pad_parms_val[0]) )
    return pad_tag_error ();
        
  $pad_when = strpos ($pad_content , '{when', 0);
  if ($pad_when === FALSE) 
    return pad_tag_error ();
  
  $pad_pos = strpos ($pad_content, '}', $pad_when);
  if ($pad_pos === FALSE) 
    return pad_tag_error ();

  $pad_case = substr ($pad_content, $pad_when+6, $pad_pos-($pad_when+6));
  if ( strlen(trim($pad_case)) == 0 )
    return pad_tag_error ();

  pad_trace ('case', 'START: ' . $pad_parms_val[0]);

  $pad_content = substr ($pad_content, $pad_pos+1);

  $pad_when = strpos($pad_content , '{when');

  while ($pad_when !== FALSE) {

    if ( ! pad_check_tag ('case', substr($pad_content, 0, $pad_when)) ) 

      $pad_when = strpos($pad_content , '{when', $pad_when+5);

    else {

      if ( $pad_parms_val[0] == pad_eval($pad_case) ) {
        pad_trace ('case', 'TRUE: ' . $pad_case);
        $pad_content = substr ($pad_content, 0, $pad_when);
        return TRUE;
      }

      pad_trace ('case', 'FALSE: ' . $pad_case);

      $pad_pos = strpos($pad_content, '}', $pad_when); 
      if ($pad_pos  === FALSE) 
        return pad_tag_error ();

      $pad_case = substr($pad_content, $pad_when+6, $pad_pos-($pad_when+6));
      if ( strlen(trim($pad_case)) == 0 )
        return pad_tag_error ();

      $pad_content = substr($pad_content, $pad_pos+1);

      $pad_when = strpos($pad_content , '{when');

    }
 
  }

  if ( ! pad_check_tag ('case', $pad_content) )
    return pad_tag_error ("Number of {case} and {/case} do not match");

  if ( $pad_parms_val[0] == pad_eval($pad_case) ) {
    pad_trace ('case', 'TRUE: ' . $pad_case);
    return TRUE;
  }
  
  pad_trace ('case', 'FALSE: ' . $pad_case);
  return FALSE;

?>