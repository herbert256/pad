<?php

  foreach ($GLOBALS as $key => $val )
    if ( substr($key, 0, 3) == 'pad' )
      global $$key;

  $Xp           = $pad;
  $XpadTraceDir = $padTraceDir;
  
  $padCnt++;

  include PAD . 'level/setup.php'; 
  include PAD . 'level/trace/start.php'; 
  include PAD . 'occurrence/trace/start.php'; 

?>