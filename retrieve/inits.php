<?php

  foreach ($GLOBALS as $key => $val )
    if ( substr($key, 0, 3) == 'pad' )
      global $$key;

  global $padApp;
  
  unset($key);
  unset($val);
  
  $padRetrieveLevel = $pad;

  include pad . 'level/setup.php'; 

  if ( $padTrace ) {
    include pad . 'level/trace/start.php'; 
    include pad . 'occurrence/trace/start.php'; 
  }

?>