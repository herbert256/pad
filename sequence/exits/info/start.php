<?php

  if ( ! isset ( $pqSetStart) )
    $pqSetStart = 'unknown/type';

  $pqSetStart = str_replace ( PQ ,      '', $pqSetStart );
  $pqSetStart = str_replace ( 'start/', '', $pqSetStart );
  $pqSetStart = str_replace ( '.php',   '', $pqSetStart );

  $pqStartP = padExplode ( $pqSetStart, '/' );
  $pqStart1 = $pqStartP [0] ?? '';
  $pqStart2 = $pqStartP [1] ?? '';

  $pqInfo ['xstart/'.$pqStart1] [] = $pqStart2;

  unset ($pqSetStart);

?>