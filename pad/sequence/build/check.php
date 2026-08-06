<?php

  // Answers "is $pqLoop a member of sequence $pqSeq?" - the whole of the check strategy.
  //
  // Included as an expression by build/one.php and by plays/play/check.php, so its return
  // value is the verdict. Uses the cheapest test the type offers, in order: its bool.php
  // predicate pqBoolXxx(), membership in the term list its fixed.php or build.php returns,
  // membership in its precomputed PADxxx constant, and failing all of those, generating
  // the sequence up to $pqLoop with pqArray() and looking in that.
  //
  // A precomputed table holds the sequence's first terms, not all of them, so it can only
  // settle the question up to the largest value in it: a hit is a member, and a miss is a
  // non-member only while the candidate is inside that range. Past it the sequence is
  // generated on rather than the question being answered no - the table used to be read as
  // the whole sequence, which reported 10001^2 not a square. The largest value is kept in
  // $padSeqCheckMax so a range of candidates does not walk the table for each of them, and
  // it is a pad* name because generating goes through a nested sequence run and
  // inits/clear.php drops every pq* on the way in.
  //
  // Generating on only makes sense for a sequence still climbing at the end of its table,
  // which is what $padSeqCheckGrow records. One that is not - negation counts downwards -
  // would never reach a stop above its largest value, so for those the table is taken as
  // the whole answer as before.
  //
  // The two generations differ in what bounds them. A type with a table is one whose from
  // and to count terms rather than name values, so it is stopped on the value it is asked
  // about; bounding that one by count would walk to the candidate as an index, which for
  // 10001^2 is a hundred million terms. A type without a table is bounded by count as
  // before, which is what keeps a sequence whose values never grow - n MOD 3 - from
  // generating for ever.
  //
  // pqBuild() picks this strategy for {keep}, {remove} and {flag}, where the question is
  // always membership rather than generation.

  if ( file_exists ( PT . "$pqSeq/bool.php" ) )

    return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );

  elseif ( file_exists ( PT . "$pqSeq/fixed.php" ) )

    return in_array ( $pqLoop, include PT . "$pqSeq/fixed.php" );

  elseif ( file_exists ( PT . "$pqSeq/build.php" ) )

    return in_array ( $pqLoop, include PT . "$pqSeq/build.php" );

  elseif ( defined ( "PAD$pqSeq" ) and count ( constant ( "PAD$pqSeq" ) ) ) {

    $pqCheckTable = constant ( "PAD$pqSeq" );

    if ( in_array ( $pqLoop, $pqCheckTable ) )
      return TRUE;

    if ( ! isset ( $padSeqCheckMax [$pqSeq] ) ) {
      $padSeqCheckMax  [$pqSeq] = max ( $pqCheckTable );
      $padSeqCheckGrow [$pqSeq] = ( end ( $pqCheckTable ) === $padSeqCheckMax [$pqSeq] );
    }

    if ( ! $padSeqCheckGrow [$pqSeq] )
      return FALSE;

    if ( is_numeric ( $pqLoop ) and $pqLoop <= $padSeqCheckMax [$pqSeq] )
      return FALSE;

    return in_array ( $pqLoop, pqArray ( $pqSeq, $pqParm, "stop=$pqLoop" ) );

  }

  return in_array ( $pqLoop, pqArray ( $pqSeq, $pqParm, "to=$pqLoop" ) );

?>