<?php

  foreach ( $pad_parms [$pad] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;
     
  $pad_walk = 'next';
  
  $pad_content = $pad_base [$pad];

  include PAD . "level/type_go.php"; 
  include PAD . "level/flags.php";

  $pad_base [$pad] = $pad_content;

  if ( $pad_walk ) {

    if ( $pad_array )
      $pad_data [$pad] = $pad_tag_result;
 
    reset ( $pad_data[$pad] );

  }
 
?> 