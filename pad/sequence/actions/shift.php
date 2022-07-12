<?php

  if ( isset($pad_seq_store [$pad_seq_pull]) )
    if ( count($pad_seq_store [$pad_seq_pull]) > $pad_seq_action_count )
      if ( $pad_seq_action_name == 'shift')
        $pad_seq_store [$pad_seq_pull] = array_slice($pad_seq_store [$pad_seq_pull], $pad_seq_action_count);
      else
        $pad_seq_store [$pad_seq_pull] = array_slice($pad_seq_store [$pad_seq_pull], 0, $pad_seq_action_count * -1);
    else
      $pad_seq_store [$pad_seq_pull] = [];

  if ( count($pad_seq_result) > $pad_seq_action_count )
    if ( $pad_seq_action_name == 'shift')
      return array_slice ( $pad_seq_result, 0, $pad_seq_action_count );
    else 
      return array_slice ( $pad_seq_result, $pad_seq_action_count * -1 );
  else
    return $pad_seq_result;
  
?>