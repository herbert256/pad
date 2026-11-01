<?php

  // A phrase in one long field: each place of its first word is looked up in the places of
  // the next by key, where in_array went through all of them for every place - 40000 places
  // of alpha against 40000 of gamma made one phrase 1.6 billion comparisons, seconds for the
  // six. The text is alpha beta gamma over and over: "gamma alpha" stands in it, the six
  // other pairs never do. The index is made, or read, before the clock starts.

  $long = [ [ 'body' => str_repeat ( 'alpha beta gamma ', 40000 ) ] ];

  padSearch ( $long, 'alpha', [ 'name' => 'longField' ] );

  $start = microtime ( TRUE );

  $never = count ( padSearch ( $long, '"alpha gamma" "beta alpha" "gamma beta" "alpha alpha" "beta beta" "gamma gamma"', [ 'name' => 'longField' ] ) );
  $found = count ( padSearch ( $long, '"gamma alpha"', [ 'name' => 'longField' ] ) );

  $phrases = "never $never, found $found, " . ( microtime ( TRUE ) - $start < 1.5 ? 'in time' : 'too slow' );

?>
