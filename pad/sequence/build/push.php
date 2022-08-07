<?php

  if ( ! $pSeq_push )
    if ($pSeq_update )
      $pSeq_push = TRUE;
    elseif ( ! $pPair and $pSeq_pull )
      $pSeq_push = TRUE;

  if ( ! $pSeq_push )
    return;

  if ( $pSeq_push === TRUE ) 
    if ( $pSeq_pull and $pSeq_pull !== TRUE )
      $pSeq_push = $pSeq_pull;
    elseif ( $pSequenceStore_get )
      $pSeq_push = $pSequenceStore_get;
    else
      $pSeq_push = $pSeq_name;

  $pSequenceStore [$pSeq_push] = $pSeq_result;

?>