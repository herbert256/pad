<?php

  if ( $pqLoop < 0 )
    return false;
 
  $pqSum = 0;

  for ($pqTriIdx = 0; $pqSum <= $pqLoop; $pqTriIdx++) {
    $pqSum = $pqSum + $pqTriIdx;
    if ($pqSum == $pqLoop)
      return true;
    elseif ($pqSum > $pqLoop)
      return false;
  }

  return false;
  
?>