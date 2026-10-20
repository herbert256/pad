<?php

  // Store stage of the wind-down: settles the store name, checks that name is free, and
  // writes the terms into $pqStore under it, so a later {pull}/{resume} can pick them up.
  //
  // A tag pair ({sequence}...{/sequence}) is there to iterate its terms, not to leave them
  // behind, so it is skipped unless push= or name= asked for the terms to be kept. Naming a
  // run is how the single-tag form stores it, so the pair form answers to the name too -
  // without that, closing the tag silently cost the store and a later {sequence myName}
  // fell through to the default counter.

  if ( $padPair [$pad] and ! $pqPush and ! $pqNameGiven )
    return;

  // A pair named without push= names its loop variable first: {sequence 3, name='i'} is the
  // loop of {$i}. Its values are kept under that name when the name is free for a store, and
  // the store pushed last stays the one {resume} and a bare {pull} take - a loop named step,
  // count or first ended the page on "Store name can not be equal to", and a loop named i
  // between a push and a {resume} took the resume for itself.

  if ( $padPair [$pad] and ! $pqPush ) {

    // Kept with its rows, as every store is, for the columns a later pull chains in: without
    // them {pull:s} after {sequence '1..3', add=10, name='s'}{/sequence} had no {$range}.

    // A pair named after the store it pulls, that shifts or pops from it, keeps what is left of
    // that store, as exits/store/set.php keeps it for the same pair without name=: the shifted
    // values were written over it, and {pull:s name='s', shift} over 1..5 left s holding 1, with
    // the rows of the remainder beside it.

    if ( is_string ( $pqNameGiven ) and ! pqStoreTaken ( $pqNameGiven ) ) {
      if ( $pqStoreUpdated and $pqNameGiven == $pqPull )
        $pqStore [$pqNameGiven] = array_values ( $pqStore [$pqPull] );
      else
        $pqStore [$pqNameGiven] = array_values ( $pqResult );
      $pqStored   = TRUE;
      $pqStoredAs = $pqNameGiven;
    }

    return;

  }

  include PQ . 'exits/store/last.php';
  include PQ . 'exits/store/check.php';
  include PQ . 'exits/store/set.php';

?>
