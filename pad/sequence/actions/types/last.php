<?php

  if ( count($pqResult) > $pqActionCnt )
    return array_slice ( $pqResult, $pqActionCnt * -1 );
  else
    return  $pqResult;
  
?>