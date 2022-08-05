<?php
     
  $pad_walk = 'next';
  
  $pad_content = $pad_base [$pad];
  include PAD . "level/type_go.php"; 
  $pad_base [$pad] = $pad_content;

  include PAD . "level/flags.php";

  if ( $pad_walk ) {

    if ( $pad_array )
      $pad_data [$pad] = $pad_tag_result;
 
    reset ( $pad_data[$pad] );

  }
 
?> 