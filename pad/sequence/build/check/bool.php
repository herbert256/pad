<?php
  
  if ( call_user_func ( 'pqBool' . ucfirst($pqSeq),  $pqLoop, $pqParm ) )
    $pqTmp = [ 1 => TRUE ];
  else 
    $pqTmp = [];

?>