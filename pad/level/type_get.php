<?php
    
  $pNs_pos = strpos($pTag, ':');

  if ( $pNs_pos ) {

    $pType = substr ($pTag, 0, $pNs_pos);
    $pTag  = substr ($pTag, $pNs_pos+1);

    if ( ! file_exists ( PAD . "types/$pType.php" ) ) 
      $pType = $false;
    
  } else {

    $pType = pGet_type_lvl ( $pTag );

  }

  $pName = $pPrms_tag ['name'] ?? $pTag;

  $pParms [$pad+1] ['tag']  = $pTag;
  $pParms [$pad+1] ['name'] = $pName;
  $pParms [$pad+1] ['type'] = $pType;

  return $pType;
  
?>