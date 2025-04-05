<?php

  $padSeqRandomOrderly    = $padPrm [$pad] ['orderly']     ?? ''; 
  $padSeqRandomDuplicates = $padPrm [$pad] ['duplicates']  ?? ''; 

  if ( ! $padSeqActionParm )
    $padSeqActionCnt = count ( $padSeqResult );

  return padSeqRandom ( $padSeqResult, $padSeqActionCnt, $padSeqRandomOrderly, $padSeqRandomDuplicates );
    
?>