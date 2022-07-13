<?php

  if ( ! $pad_seq_push )
    if ($pad_seq_update )
      $pad_seq_push = TRUE;
    elseif ( ! $pad_pair and $pad_seq_pull )
      $pad_seq_push = TRUE;

  if ( ! $pad_seq_push )
    return $pad_seq_result;

  if ( $pad_seq_push === TRUE ) 
    if ( $pad_seq_pull and $pad_seq_pull !== TRUE )
      $pad_seq_push = $pad_seq_pull;
    elseif ( $pad_seq_pull )
      $pad_seq_push = $pad_seq_pull;
    else
      $pad_seq_push = $pad_seq_name;

  $pad_seq_store [$pad_seq_push] = $pad_seq_result;
  
?>