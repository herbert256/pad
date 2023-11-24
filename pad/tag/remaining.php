<?php

  global $padData, $padOccur;
  
  $padWrk = count ( $padData [$padIdx] ) - $padOccur [$padIdx];

  if ($padWrk < 0)
    return 0;
  else 
    return $padWrk;

?>