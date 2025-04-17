<?php

  $pqTemp = include "sequence/types/$pqSeq/fixed.php";

  if ( isset ( $pqTemp [$pqLoop-1] ) )
    return $pqTemp [$pqLoop-1];
  else
    return FALSE;

?>