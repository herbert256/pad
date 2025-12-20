<?php

  if ( $pqRandomParm and $pqSeq <> 'range' ) {
    $pqParm = $pqRandomParm;
    pqRandomParm ( $pqParm );
  }

  return $pqParm;

?>
