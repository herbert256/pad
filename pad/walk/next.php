<?php

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;
     
  $pad_walk = 'next';
  
  $pad_content = $pad_base [$pad_lvl];

  include PAD . "level/type.php"; 
  include PAD . "level/flags.php";

  $pad_base [$pad_lvl] = $pad_content;

  if ( $pad_walk ) {

    if ( $pad_array )
      $pad_data [$pad_lvl] = $pad_tag_result;
 
    reset ( $pad_data[$pad_lvl] );

  }
 
?> 