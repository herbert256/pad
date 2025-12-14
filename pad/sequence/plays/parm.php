<?php

  if ( $pqParm and isset ( $pqStore [$pqParm] ) )
    $pqParm = $pqStore [$pqParm] [ count ( $pqResult ) ];

  if ( str_contains ( $pqParm, '..' ) and $pqSeq <> 'range' )
    pqRandomParm ( $pqParm );

  return $pqParm;

?>