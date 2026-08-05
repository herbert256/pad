<?php

  // Info block: records where the sequence subsystem was entered. Every start/ handler stamps
  // its own __FILE__ into $pqSetStart; this strips that back to "<kind>/<name>" (kind being
  // tags, types or eval) and files it as $pqInfo['start/<kind>'][] = <name>, then unsets it
  // so the next run cannot inherit it. 'unknown/type' when nothing stamped it.

  if ( ! isset ( $pqSetStart) )
    $pqSetStart = 'unknown/type';

  $pqSetStart = str_replace ( PQ ,      '', $pqSetStart );
  $pqSetStart = str_replace ( 'start/', '', $pqSetStart );
  $pqSetStart = str_replace ( '.php',   '', $pqSetStart );

  $pqStartP = padExplode ( $pqSetStart, '/' );
  $pqStart1 = $pqStartP [0] ?? '';
  $pqStart2 = $pqStartP [1] ?? '';

  $pqInfo ['start/'.$pqStart1] [] = $pqStart2;

  unset ($pqSetStart);

?>