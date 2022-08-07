<?php
     
  $pad_walk = 'next';
  
  $pContent = $pBase[$p];
  include PAD . "level/type_go.php"; 
  $pBase[$p] = $pContent;

  include PAD . "level/flags.php";

  if ( $pad_walk ) {

    if ( $pArray )
      $pData[$p] = $pTagResult;
 
    reset ( $pData[$p] );

  }
 
?> 