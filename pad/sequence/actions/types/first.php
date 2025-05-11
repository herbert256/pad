<?php

  if ( count($pqResult) > $pqActionCnt )
    $pqResult = array_slice ( $pqResult, 0, $pqActionCnt );
  
?>