<?php

  if ( $pTrace ) 
    include 'trace/end.php';
  
  $pResult [$p] .= $pHtml [$p];

  if ( $p )
    pReset ($p);

?>