<?php

  if ( count($pqResult) > $pqActionCnt )
    return array_slice ( $pqResult, 0, $pqActionCnt );
  else
    return $pqResult;
  
?>