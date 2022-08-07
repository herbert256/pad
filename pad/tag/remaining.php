<?php

  global $pData, $pOccur;
  
  $pWrk = count ( $pData [$pIdx] ) - $pOccur [$pIdx];

  if ($pWrk < 0)
    return 0;
  else 
    return $pWrk;

?>