<?php

  $exampleType = $exampleType ?? 'ajax';
  
  if ( ! isset ( $fromMenu ) )
    return FALSE;

  set_time_limit(3);

  $title = "All PAD pages with the example tag";
 
?>