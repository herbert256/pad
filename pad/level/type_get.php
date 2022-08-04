<?php
    
  $pad_ns_pos = strpos($pad_tag, ':');

  if ( $pad_ns_pos ) {

    $pad_tag_type = substr ($pad_tag, 0, $pad_ns_pos);
    $pad_tag      = substr ($pad_tag, $pad_ns_pos+1);

    if ( ! file_exists ( PAD . "types/$pad_tag_type.php" ) ) 
      $pad_tag_type = $false;
    
  } else {

    $pad_tag_type = pad_get_type_lvl ( $pad_tag );

  }

  $pad_name = $pad_prms_tag ['name'] ?? $pad_tag;

  $pad_parms [$pad] ['tag']      = $pad_tag;
  $pad_parms [$pad] ['name']     = $pad_name;
  $pad_parms [$pad] ['tag_type'] = $pad_tag_type;

  return $pad_tag_type;
  
?>