<?php

  $pad_tst     = '{when';
  $pad_basis   = pad_eval ($pad_parms);        
  $pad_chk     = strpos   ($pad_content , $pad_tst);
  $pad_pos     = strpos   ($pad_content, '}', $pad_chk);
  $pad_eval    = substr   ($pad_content, $pad_chk+6, $pad_pos-($pad_chk+6));
  $pad_content = substr   ($pad_content, $pad_pos+1);

  return include 'go/if_case.php';

?>