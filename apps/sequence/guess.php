<?php

  // Guess the next term: the first seven terms of a well-known sequence, picked at random,
  // and a form that asks for the eighth. The form sends back which sequence it was by its
  // place in the list (pick) with the guess; ?guess&pick=0 asks about a given one.
  //
  // pick and guess arrive from the request, so they are only used when they make sense:
  // a pick outside the list starts a new round, and a guess is compared as text, trimmed.

  $choices = seqFunGuessable ();

  if ( isset ( $pick ) and is_string ( $pick ) and ctype_digit ( $pick ) and isset ( $choices [ (int) $pick ] ) )
    $pick = (int) $pick;
  else {
    $pick  = random_int ( 0, count ( $choices ) - 1 );
    $guess = '';
  }

  $type     = $choices [$pick];
  $terms    = seqFunTerms ( $type, 14 );
  $shown    = implode ( ', ', array_slice ( $terms, 0, 7 ) );
  $next     = $terms [7];
  $rest     = implode ( ', ', array_slice ( $terms, 8 ) );
  $answered = ( isset ( $guess ) and is_string ( $guess ) and trim ( $guess ) !== '' ) ? 1 : 0;
  $right    = ( $answered and trim ( $guess ) === (string) $next ) ? 1 : 0;
  $oeis     = seqFunOeis ( $terms );
  $oeis     = $oeis ? sprintf ( 'A%06d', $oeis ) : '';

?>
