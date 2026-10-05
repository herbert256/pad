<?php

  // padRetry calls with the attempt number until the callback stops throwing; after the
  // last attempt, or when $when says no, the Throwable is thrown on. The waits come
  // between the attempts: one number for each, or a list whose last one repeats.

  $tried = [];

  $answer = padRetry ( 3, function ( $attempt ) use ( &$tried ) {
    $tried [] = $attempt;
    if ( $attempt < 3 )
      throw new RuntimeException ( "attempt $attempt failed" );
    return "done on attempt $attempt";
  } );

  $first = padRetry ( 5, fn ( $attempt ) => "at once on $attempt" );

  $gaveUp = [];

  try {
    padRetry ( 2, function ( $attempt ) use ( &$gaveUp ) { $gaveUp [] = $attempt; throw new RuntimeException ( "no luck $attempt" ); } );
  } catch ( RuntimeException $e ) {
    $gaveUp [] = $e->getMessage ();
  }

  $stopped = [];

  try {
    padRetry ( 5, function ( $attempt ) use ( &$stopped ) { $stopped [] = $attempt; throw new LogicException ( 'a bug, not bad luck' ); },
               0, fn ( $e ) => $e instanceof RuntimeException );
  } catch ( LogicException $e ) {
    $stopped [] = get_class ( $e );
  }

  $errors = [];

  $caught = padRetry ( 2, function ( $attempt ) use ( &$errors ) {
    $errors [] = $attempt;
    return ( $attempt == 1 ) ? intdiv ( 1, 0 ) : 'an Error is retried too';
  } );

  $start  = hrtime ( TRUE );
  padRetry ( 3, function ( $attempt ) { if ( $attempt < 3 ) throw new RuntimeException; }, 15 );
  $waited = ( hrtime ( TRUE ) - $start ) / 1e6 >= 30 ? 'waited twice' : 'did not wait';

  $start  = hrtime ( TRUE );
  padRetry ( 4, function ( $attempt ) { if ( $attempt < 4 ) throw new RuntimeException; }, [ 1, 20 ] );
  $listed = ( hrtime ( TRUE ) - $start ) / 1e6 >= 41 ? 'the last wait repeats' : 'it did not';

  $r = json_encode ( [ $answer, $tried, $first, $gaveUp, $stopped, $caught, $errors, $waited, $listed ] );

?>
