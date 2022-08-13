<?php
    
  $pNs = strpos($pTag [$p], ':');

  if ( $pNs ) {

    $pType [$p] = substr ($pTag [$p], 0, $pNs);
    $pTag  [$p] = substr ($pTag [$p], $pNs+1);

    if ( ! file_exists ( PAD . "types/$pType[$p].php" ) ) 
      $pType [$p] = FALSE;
    
  } else

    $pType [$p] = pGet_type_lvl ( $pTag [$p] );

  return $pType [$p];  

?>