<?php

  if ( $pSeq_action_value === TRUE or ! ctype_digit($pSeq_action_value) )
    if ( $pSeq_count )
      $pSeq_action_count = $pSeq_count;
    else
      $pSeq_action_count = 1;
  else
    $pSeq_action_count = $pSeq_action_value;    

  if ( count($pSeq_result) > $pSeq_action_count )
    if ( $pSeq_action_name == 'first')
      return array_slice ( $pSeq_result, 0, $pSeq_action_count );
    else 
      return array_slice ( $pSeq_result, $pSeq_action_count * -1 );
  else
    return $pSeq_result;
  
?>