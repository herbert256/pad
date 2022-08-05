<?php
    
  $pad_ns_pos = strpos($pad_tag, ':');

  if ( $pad_ns_pos ) {

    $pad_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag  = substr ($pad_tag, $pad_ns_pos+1);

    if ( ! file_exists ( PAD . "types/$pad_type.php" ) ) 
      $pad_type = $false;
    
  } else {

    $pad_type = pad_get_type_lvl ( $pad_tag );

  }

  $pad_name = $pad_prms_tag ['name'] ?? $pad_tag;

  $pad_parms [$pad+1] ['tag']  = $pad_tag;
  $pad_parms [$pad+1] ['name'] = $pad_name;
  $pad_parms [$pad+1] ['type'] = $pad_type;

  return $pad_type;
  
?>