<?php

  if ( count($pqResult) > $pqActionCnt )
    if ( $pqAction == 'first')
      return array_slice ( $pqResult, 0, $pqActionCnt );
    else 
      return array_slice ( $pqResult, $pqActionCnt * -1 );
  else
    return $pqResult;
  
?>