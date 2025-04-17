<?php

  $pqRandomOrderly    = $padPrm [$pad] ['orderly']     ?? ''; 
  $pqRandomDuplicates = $padPrm [$pad] ['duplicates']  ?? ''; 

  if ( ! $pqActionParm )
    $pqActionCnt = count ( $pqResult );

  return pqRandom ( $pqResult, $pqActionCnt, $pqRandomOrderly, $pqRandomDuplicates );
    
?>