<?php
     
  $pWalk [$p] = 'next';
  
  $pContent = $pBase [$p];
  include PAD . "level/type_go.php"; 
  $pBase [$p] = $pContent;

  include PAD . "level/flags.php";

  if ( $pWalk [$p] ) {

    if ( $pArray )
      $pData [$p] = $pTagResult;
    elseif ( $pText ) 
      $pBase [$p] = $pTagResult;
    elseif ( $pElse ) 
      $pBase [$p] = $pFalse [$p];
 
    reset ( $pData [$p] );

  }

?> 