<?php
     
  $pad_walk = 'next';
  
  $pContent = $pBase [$pad];
  include PAD . "level/type_go.php"; 
  $pBase [$pad] = $pContent;

  include PAD . "level/flags.php";

  if ( $pad_walk ) {

    if ( $pArray )
      $pData [$pad] = $pTag_result;
 
    reset ( $pData[$pad] );

  }
 
?> 