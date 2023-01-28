<?php

  foreach ($GLOBALS as $key => $val )
    if ( substr($key, 0, 3) == 'pad' )
      global $$key;

  $Xp           = $pad;
  $XpadTraceDir = $padTraceDir;
  
  $padCnt++;

  include PAD . 'pad/level/setup.php'; 
  include PAD . 'pad/level/trace/start.php'; 
  include PAD . 'pad/occurrence/trace/start.php'; 

?>