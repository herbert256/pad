<?php

  if ( $pTrace_occurence ) 
    include 'trace/end.php';
  
  $pResult [$pad] .= $pHtml [$pad];

  $pOccur_dir = $pLevel_dir;
  $pParms [$pad] ['occur_dir'] = $pOccur_dir ;

  pReset ($pad);

?>