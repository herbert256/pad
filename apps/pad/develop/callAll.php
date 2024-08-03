<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  set_time_limit ( 300 );

  foreach ( padList ( 0 ) as $one )
    $curl  = getPage ( $one ['item'] , 1 );

  echo "done";
    
?>