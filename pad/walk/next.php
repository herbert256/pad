<?php
     
  $pWalk[$p] = 'next';
  
  $pContent = $pBase[$p];
  include PAD . "level/type_go.php"; 
  $pBase[$p] = $pContent;

  include PAD . "level/flags.php";

  if ( $pWalk[$p] ) {

    if ( $pArray )
      $pData[$p] = $pTagResult;
 
    reset ( $pData[$p] );

  }
 
?> 