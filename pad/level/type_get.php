<?php
    
  $pNs_pos = strpos($pTag[$p], ':');

  if ( $pNs_pos ) {

    $pType = substr ($pTag[$p], 0, $pNs_pos);
    $pTag[$p] = substr ($pTag[$p], $pNs_pos+1);

    if ( ! file_exists ( PAD . "types/$pType.php" ) ) 
      $pType = $false;
    
  } else {

    $pType = pGet_type_lvl ( $pTag[$p]);

  }

  $pName[$p] = $pName[$p] ?? $pTag[$p];

  $ [$p+1] ['tag']  = $pTag[$p];
  $ [$p+1] ['name'] = $pName[$p];
  $ [$p+1] ['type'] = $pType;

  return $pType;
  
?>