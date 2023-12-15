<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  set_time_limit ( 30 );

  foreach ( padList ( 0 ) as $one )
    getPage ( $one ['item'], 1 );

  echo "done";

?>