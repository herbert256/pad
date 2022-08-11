<?php

  $pWalk [$p] = 'end';

  $pContent = $pResult [$p];
  include PAD . "level/type_go.php"; 
  $pResult [$p] = $pContent;

  include PAD . "level/flags.php";

  // if ( $pNull ) 
  //   $pResult [$p] = '';
  // elseif ( $pArray )
  //   $pData [$p] = $pTagResult [$p];
  // elseif ( $pText ) 
  //   $pResult [$p] = $pTagResult [$p];
  // elseif ( $pElse ) 
  //   $pResult [$p] = $pFalse [$p];
 

?>