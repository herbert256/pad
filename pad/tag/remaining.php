<?php

  global $pData, $pOccur;
  
  $pad_wrk = count ( $pData [$pIdx] ) - $pOccur [$pIdx];

  if ($pad_wrk < 0)
    return 0;
  else 
    return $pad_wrk;

?>