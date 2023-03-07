<?php

  foreach ($GLOBALS as $key => $val )
    if ( substr($key, 0, 3) == 'pad' )
      global $$key;

  global $app;
  
  unset($key);
  unset($val);
  
  $padRetrieveLevel = $pad;

  include PAD . 'level/setup.php'; 

  if ( $padTrace ) {
    include PAD . 'level/trace/start.php'; 
    include PAD . 'occurrence/trace/start.php'; 
  }

?>