<?php

  $padSeqRandomOrderly    = $padPrm [$pad] ['orderly']     ?? ''; 
  $padSeqRandomDuplicates = $padPrm [$pad] ['duplicates']  ?? ''; 

  return padSeqRandom ( $padSeqResult, $padSeqActionCnt, $padSeqRandomOrderly, $padSeqRandomDuplicates );
    
?>