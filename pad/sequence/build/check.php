<?php

  // Answers "is $pqLoop a member of sequence $pqSeq?" - the whole of the check strategy.
  //
  // Included as an expression by build/one.php and by plays/play/check.php, so its return
  // value is the verdict. Uses the cheapest test the type offers, in order: its bool.php
  // predicate pqBoolXxx(), membership in the term list its fixed.php or build.php returns,
  // membership in its precomputed PADxxx constant, and failing all of those, generating
  // the sequence up to $pqLoop with pqTerms() and looking in that. Which of those a type
  // offers is settled once a request by pqCheckKind() in lib/sequence.php: it was three
  // file_exists() calls for every candidate.
  //
  // A list is looked in through an index of its values, made once per sequence and
  // parameter by pqIndex() - the value's first position under the key pqIndexKey() makes of
  // it, so 5, '5' and 5.0 are one value as in_array () takes them. in_array () walked the
  // whole list for every candidate: a keep over 3000 values looked through the 10,000 terms
  // of the recaman table 3000 times, and a flag of kolakoski through its 55,893 terms.
  //
  // A precomputed table holds the sequence's first terms, not all of them, so it can only
  // settle the question up to the largest value in it: a hit is a member, and a miss is a
  // non-member only while the candidate is inside that range. Past it the sequence is
  // generated on rather than the question being answered no - the table used to be read as
  // the whole sequence, which reported 10001^2 not a square. The index and the largest
  // value are kept in $padSeqCheckTable, a pad* name so it outlives the run: inits/clear.php
  // drops every pq* when the next tag starts.
  //
  // Generating on only makes sense for a sequence still climbing at the end of its table,
  // which is what the table's grow mark records. One that is not - negation counts
  // downwards - would never reach a stop above its largest value, so for those the table is
  // taken as the whole answer as before.
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

  $pqCheckKind = pqCheckKind ( $pqSeq );

  if ( $pqCheckKind == 'bool' )
    return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );

  $pqCheckAt = pqIndexKey ( $pqLoop );

  // A list a type returns whole is made from its parameter - list and range, oeis by its
  // A-number - and a range also from the increment and the tag's own first parameter, so
  // the index is kept per all of those.

  if ( $pqCheckKind == 'fixed' or $pqCheckKind == 'build' ) {

    $pqCheckKey = $pqCheckKind . '|' . $pqSeq . '|' . pqCheckParm ( $pqParm ) . '|' . pqCheckParm ( $pqInc )
                . '|' . pqCheckParm ( $padParm ?? '' );

    if ( ! isset ( $padSeqCheckIndex [$pqCheckKey] ) )
      $padSeqCheckIndex [$pqCheckKey] = pqIndex ( include PT . "$pqSeq/$pqCheckKind.php" );

    return isset ( $padSeqCheckIndex [$pqCheckKey] [$pqCheckAt] );

  }

  if ( $pqCheckKind == 'table' ) {

    if ( ! isset ( $padSeqCheckTable [$pqSeq] ) ) {
      $pqCheckTable = constant ( "PAD$pqSeq" );
      $padSeqCheckTable [$pqSeq] = [
        'index' => pqIndex ( $pqCheckTable ),
        'max'   => max ( $pqCheckTable ),
        'grow'  => ( end ( $pqCheckTable ) === max ( $pqCheckTable ) )
      ];
    }

    if ( isset ( $padSeqCheckTable [$pqSeq] ['index'] [$pqCheckAt] ) )
      return TRUE;

    if ( ! $padSeqCheckTable [$pqSeq] ['grow'] )
      return FALSE;

    // A value that is no number is no term of a table's numbers either: generating on to a
    // stop it named never reached it, and {sequence 'a;9;b', flag, square} spent the whole
    // time limit on its first candidate.

    if ( ! is_numeric ( $pqLoop ) or $pqLoop <= $padSeqCheckTable [$pqSeq] ['max'] )
      return FALSE;

    // Past the table the terms are generated on to a stop at or above the candidate once per
    // sequence and parameter, and again with the stop doubled when a later candidate passes
    // it - generated afresh for every candidate, a flag of identity over 10001..14000 built
    // ten thousand terms four thousand times and ran into the time limit.

    $pqCheckKey = $pqSeq . '|' . pqCheckParm ( $pqParm );

    if ( ! isset ( $padSeqCheckStop [$pqCheckKey] ) or $padSeqCheckStop [$pqCheckKey] [0] < $pqLoop ) {
      $pqCheckStop = max ( $pqLoop, 2 * ( $padSeqCheckStop [$pqCheckKey] [0] ?? $padSeqCheckTable [$pqSeq] ['max'] ) );
      $padSeqCheckStop [$pqCheckKey] = [ $pqCheckStop, pqIndex ( pqTerms ( $pqSeq, $pqParm, [ 'stop' => $pqCheckStop ] ) ) ];
    }

    return isset ( $padSeqCheckStop [$pqCheckKey] [1] [$pqCheckAt] );

  }

  // The terms up to the candidate's position, generated once per sequence and parameter and
  // extended by doubling: keep and remove asked for a fresh nested run for every candidate,
  // quadratic over a long list. Kept under a pad* name for the reason above. The question is
  // the same as before - is the value among the first $pqLoop terms - which the index
  // answers with the first position it holds for the value.

  // Terms that can come out otherwise another time - a type that draws at random or evaluates
  // an expression, a parameter naming a store or drawn from a range (pqTermsKey) - are kept for
  // this run only, under a pq* name inits/clear.php drops when the next tag starts: kept for
  // the request, {sequence '1..12', keep, eval='@*$n'} in a loop over $n kept the first $n's
  // multiples every time, and keep, chance=2 repeated one random pick.

  if ( pqTermsKey ( $pqSeq, '', $pqParm ) === '' )
    $pqCheckTerms = &$pqCheckRun;
  else
    $pqCheckTerms = &$padSeqCheckList;

  $pqCheckKey = $pqSeq . '|' . pqCheckParm ( $pqParm );
  $pqCheckTo  = max ( 1, (int) $pqLoop );

  if ( ! isset ( $pqCheckTerms [$pqCheckKey] ) or $pqCheckTerms [$pqCheckKey] [0] < $pqCheckTo ) {
    $pqCheckTo = max ( $pqCheckTo, 2 * ( $pqCheckTerms [$pqCheckKey] [0] ?? 0 ) );
    $pqCheckTerms [$pqCheckKey] = [ $pqCheckTo, pqIndex ( pqTerms ( $pqSeq, $pqParm, [ 'to' => $pqCheckTo ] ) ) ];
  }

  return ( $pqCheckTerms [$pqCheckKey] [1] [$pqCheckAt] ?? PHP_INT_MAX ) <= max ( 1, (int) $pqLoop );

?>
