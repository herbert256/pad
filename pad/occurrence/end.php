<?php

  if ( $pTrace_occurence ) 
    include 'trace/end.php';
  
  $pResult [$p] .= $pHtml [$p];

  $pOccur_dir = $pLevel_dir;
  $pParms [$p] ['occur_dir'] = $pOccur_dir ;

  pReset ($pad);

?>