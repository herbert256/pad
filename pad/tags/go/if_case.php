<?php
  
  $pad_chk = strpos ($pad_content, $pad_tst);
  $pad_add = strlen ($pad_tst);
  
  while ($pad_chk !== FALSE) {

    if ( ! pad_check_tag ($pad_tag, substr($pad_content, 0, $pad_chk)) ) 

      $pad_chk = strpos($pad_content , $pad_tst, $pad_chk+$pad_add);

    else {

      $pad_eval = pad_eval($pad_eval);

      if ( ($pad_tag == 'case' and $pad_basis == $pad_eval) or ($pad_tag == 'if' and $pad_eval) ) {
        $pad_content = substr ($pad_content, 0, $pad_chk);
        return TRUE;
      }

      $pad_pos     = strpos($pad_content, '}', $pad_chk); 
      $pad_eval    = substr($pad_content, $pad_chk+$pad_add+1, $pad_pos-($pad_chk+$pad_add+1));
      $pad_content = substr($pad_content, $pad_pos+1);
      $pad_chk     = strpos($pad_content, $pad_tst);

    }
 
  }

  $pad_eval = pad_eval($pad_eval);

  return ( ($pad_tag == 'case' and $pad_basis == $pad_eval) or ($pad_tag == 'if' and $pad_eval) );

  if ( ($pad_tag == 'case' and $pad_basis == $pad_eval) or ($pad_tag == 'if' and $pad_eval) )
    return TRUE;
  
  return FALSE;;

?>