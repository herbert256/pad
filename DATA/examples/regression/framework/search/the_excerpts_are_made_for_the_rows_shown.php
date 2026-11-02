<?php

  // 3000 rows a query of short prefixes finds every one of: the excerpts are made for the
  // rows the page can show - the ten of first=10 - and padSearch's first answers the best
  // rows only, where every hit had its excerpt made before first= cut them, and a search
  // cost what its hits cost, not what the page showed: 0.53 seconds for the ten, 0.1 now.

  $syllables = [ 'ka', 'lo', 'mi', 'ter', 'san', 'dor', 'vel', 'pra', 'nu', 'qui', 'zen', 'bar', 'fo', 'lit', 'ron', 'ge' ];
  $seed      = 11;
  $next      = function ( $n ) use ( &$seed ) {
    $seed = ( $seed * 1103515245 + 12345 ) % 2147483648;
    return intdiv ( $seed, 65536 ) % $n;
  };

  $hitRows = [];
  for ( $r = 0; $r < 3000; $r++ ) {
    $body = [];
    for ( $w = 0; $w < 60; $w++ )
      $body [] = $syllables [ $next ( 16 ) ] . $syllables [ $next ( 16 ) ];
    $hitRows [] = [ 'id' => $r, 'body' => implode ( ' ', $body ) ];
  }

  unset ( $body );

  $prefixes = implode ( ' ', array_map ( fn ( $s ) => "$s*", $syllables ) );

  // The time of all hits with their excerpts is the measure: the ten best take a part of it.

  padSearch ( $hitRows, 'kalo', [ 'name' => 'hitRows' ] );

  $start  = hrtime ( TRUE );
  $all    = count ( padSearch ( $hitRows, $prefixes, [ 'name' => 'hitRows' ] ) );
  $allNs  = hrtime ( TRUE ) - $start;

  $start  = hrtime ( TRUE );
  $best   = padSearch ( $hitRows, $prefixes, [ 'name' => 'hitRows', 'first' => 3 ] );
  $shown  = "$all hits, " . count ( $best ) . ' best, ' . ( hrtime ( TRUE ) - $start < $allNs / 2 ? 'in part of the time' : 'in all of it' );

  $hitStart = hrtime ( TRUE );

?>
