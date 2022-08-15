<?php

  if ( ! $padSeq_push )
    if ($padSeq_update )
      $padSeq_push = TRUE;
    elseif ( ! $padPair and $padSeq_pull )
      $padSeq_push = TRUE;

  if ( ! $padSeq_push )
    return;

  if ( $padSeq_push === TRUE ) 
    if ( $padSeq_pull and $padSeq_pull !== TRUE )
      $padSeq_push = $padSeq_pull;
    elseif ( $padSequenceStore_get )
      $padSeq_push = $padSequenceStore_get;
    else
      $padSeq_push = $padSeq_name;

  $padSequenceStore [$padSeq_push] = $padSeq_result;

?>