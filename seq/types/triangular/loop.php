<?php

  if ( $padSeqLoop < 0 )
    return false;
 
  $padSeqSum = 0;

  for ($padSeqTriIdx = 0; $padSeqSum <= $padSeqLoop; $padSeqTriIdx++) {
    $padSeqSum = $padSeqSum + $padSeqTriIdx;
    if ($padSeqSum == $padSeqLoop)
      return true;
    elseif ($padSeqSum > $padSeqLoop)
      return false;
  }

  return false;
  
?>