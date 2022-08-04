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

  return $pad_tag_type;
  
?>