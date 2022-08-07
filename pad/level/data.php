<?php

  if ( $pNull )

    $pNow = [];

  elseif ( $pElse )

    if     ( $pArray                   ) $pNow = array_slice ($pTagResult, 0, 1); 
    elseif ( count ($pData[$p]) ) $pNow = array_slice ($pData[$p], 0, 1); 
    else                                    $pNow = pDefault_data ();  

  elseif ( $pArray )

    $pNow = $pTagResult;

  else 

    $pNow = $pData[$p];

  $pData[$p] = pMake_data ( $pNow );   

?>