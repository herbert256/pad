<?php

  // Answers "is $pqLoop a member of sequence $pqSeq?" - the whole of the check strategy.
  //
  // Included as an expression by build/one.php and by plays/play/check.php, so its return
  // value is the verdict. Uses the cheapest test the type offers, in order: its bool.php
  // predicate pqBoolXxx(), membership in the term list its fixed.php or build.php returns,
  // membership in its precomputed PADxxx constant, and failing all of those, generating
  // the sequence up to $pqLoop with pqArray() and looking in that.
  //
  // pqBuild() picks this strategy for {keep}, {remove} and {flag}, where the question is
  // always membership rather than generation.

  if ( file_exists ( PT . "$pqSeq/bool.php" ) )

    return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );

  elseif ( file_exists ( PT . "$pqSeq/fixed.php" ) )

    return in_array ( $pqLoop, include PT . "$pqSeq/fixed.php" );

  elseif ( file_exists ( PT . "$pqSeq/build.php" ) )

    return in_array ( $pqLoop, include PT . "$pqSeq/build.php" );

  elseif ( defined ( "PAD$pqSeq" ) )

    return in_array ( $pqLoop, constant ( "PAD$pqSeq" ) );

  return in_array ( $pqLoop, pqArray ( $pqSeq, $pqParm, "to=$pqLoop" ) );

?>