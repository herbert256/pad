<?php

  if ( $pNull [$p] )

    $pNow = [];

  elseif ( $pElse [$p] )

    if     ( $pArray [$p]        ) $pNow = array_slice ($pTagResult [$p], 0, 1); 
    elseif ( count ($pData [$p]) ) $pNow = array_slice ($pData [$p], 0, 1); 
    else                           $pNow = pDefault_data ();  

  elseif ( $pArray [$p] )

    $pNow = $pTagResult [$p];

  else 

    $pNow = $pData [$p];

  $pData [$p] = pMake_data ( $pNow );   

?>