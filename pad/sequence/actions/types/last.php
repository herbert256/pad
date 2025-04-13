<?php

  if ( count($padSeqResult) > $padSeqActionCnt )
    if ( $padSeqAction == 'first')
      return array_slice ( $padSeqResult, 0, $padSeqActionCnt );
    else 
      return array_slice ( $padSeqResult, $padSeqActionCnt * -1 );
  else
    return include 'sequence/actions/types/first.php';
  
?>