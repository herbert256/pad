<?php

  if ( $pad_seq_action_value === TRUE or ! ctype_digit($pad_seq_action_value) )
    if ( $pad_seq_count )
      $pad_seq_action_count = $pad_seq_count;
    else
      $pad_seq_action_count = 1;
  else
    $pad_seq_action_count = $pad_seq_action_value;    

  if ( count($pad_seq_result) > $pad_seq_action_count )
    if ( $pad_seq_action_name == 'first')
      return array_slice ( $pad_seq_result, 0, $pad_seq_action_count );
    else 
      return array_slice ( $pad_seq_result, $pad_seq_action_count * -1 );
  else
    return $pad_seq_result;
  
?>