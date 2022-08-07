<?php
    
  $pNs = strpos($pTag[$p], ':');

  if ( $pNs ) {

    $pType[$p] = substr ($pTag[$p], 0, $pNs_pos);
    $pTag[$p]  = substr ($pTag[$p], $pNs_pos+1);

    if ( ! file_exists ( PAD . "types/$pType.php" ) ) 
      $pType[$p] = $false;
    
  } else

    $pType[$p] = pGet_type_lvl ( $pTag[$p] );

  $pName[$p] = $pName[$p] ?? $pTag[$p];

?>