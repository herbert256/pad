<?php

  if ( count($pad_seq_result) > $pad_seq_action_count )
    if ( $pad_seq_action_name == 'first')
      return array_slice ( $pad_seq_result, 0, $pad_seq_action_count );
    else 
      return array_slice ( $pad_seq_result, $pad_seq_action_count * -1 );
  else
    return include 'first.php';
  
?>