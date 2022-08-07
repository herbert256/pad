<?php

  if ( count($pSeq_result) > $pSeq_action_count )
    if ( $pSeq_action_name == 'first')
      return array_slice ( $pSeq_result, 0, $pSeq_action_count );
    else 
      return array_slice ( $pSeq_result, $pSeq_action_count * -1 );
  else
    return include 'first.php';
  
?>