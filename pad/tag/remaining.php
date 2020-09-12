<?php

  global $pad_data, $pad_occur;
  
  $pad_wrk = count ( $pad_data [$pad_idx] ) - $pad_occur [$pad_idx];

  if ($pad_wrk < 0)
    return 0;
  else 
    return $pad_wrk;

?>