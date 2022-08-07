<?php

  if ( $pNull )

    $pNow = [];

  elseif ( $pElse )

    if     ( $pArray                   ) $pNow = array_slice ($pTag_result, 0, 1); 
    elseif ( count ($pData [$pad]) ) $pNow = array_slice ($pData [$pad], 0, 1); 
    else                                    $pNow = pDefault_data ();  

  elseif ( $pArray )

    $pNow = $pTag_result;

  else 

    $pNow = $pData [$pad];

  $pData [$pad] = pMake_data ( $pNow );   

?>