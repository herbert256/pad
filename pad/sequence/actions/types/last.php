<?php

  if ( count($pqResult) > $pqActionCnt )
    $pqResult = array_slice ( $pqResult, $pqActionCnt * -1 );

?>