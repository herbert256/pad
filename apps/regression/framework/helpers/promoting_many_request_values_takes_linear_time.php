<?php

  // Four thousand request values of names the page has not used are promoted in time that
  // grows with their number: each was asked array_key_exists ( $name, $GLOBALS ), and since
  // PHP 8.1 that copies the whole symbol table, which every promoted value made larger - a
  // square of the number, 50 to 125 ms for these and 315 ms for 10000 values, on every
  // request of any visitor who sends them. Now about 1 ms, well under the 20 ms asked here.

  $zzValues = [];

  for ( $zzI = 0; $zzI < 4000; $zzI++ )
    $zzValues ["zzLinear$zzI"] = " v$zzI ";

  $zzStart = hrtime ( TRUE );

  padGetParms ( 'POST', $zzValues );

  $zzTime = ( ( hrtime ( TRUE ) - $zzStart ) / 1e6 < 20 ) ? 'linear' : 'too slow';
  $zzSet  = $GLOBALS ['zzLinear0'] . ' ' . $GLOBALS ['zzLinear3999'];

?>
