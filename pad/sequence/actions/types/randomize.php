<?php

  $pqRandomOrderly    = $padPrm [$pad] ['orderly']     ?? ''; 
  $pqRandomDuplicates = $padPrm [$pad] ['duplicates']  ?? ''; 
  $pqRandomOnce       = $padPrm [$pad] ['atLeastOnce'] ?? ''; 

  if ( ! $pqActionParm )
    $pqActionCnt = count ( $pqResult );

  $pqResult = pqRandom ( $pqResult, $pqActionCnt, $pqRandomOrderly, $pqRandomDuplicates, $pqRandomOnce );
    
?>