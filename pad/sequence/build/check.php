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
  // the index is kept per all of those, under the key pqListKey () keeps for them.

  if ( $pqCheckKind == 'fixed' or $pqCheckKind == 'build' ) {

    $pqCheckKey = pqListKey ( $pqCheckKind, $pqSeq, $pqParm, $pqInc, $padParm ?? '' );

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

  // The terms as far as the larger of the candidate and the reach a run has by default,
  // $padSeqDefaultTries, generated once per sequence and parameter and extended by doubling -
  // keep and remove asked for a fresh nested run for every candidate, quadratic over a long
  // list - and the value is a member at whatever position it stands. Kept under a pad* name
  // for the reason above. The question was whether the value is among the first $pqLoop
  // terms, which holds only for a sequence that climbs a whole number at a time:
  // eval='100 - @' makes 1 at position 99 and was denied it, eval='@ % 4' its 0 at position
  // 4, and step=2.5 answered 3.5 by the order the values were asked in.

  // Terms that can come out otherwise another time - a type that draws at random or evaluates
  // an expression, a parameter naming a store or drawn from a range (pqTermsKey) - are kept for
  // this run only, under a pq* name inits/clear.php drops when the next tag starts: kept for
  // the request, {sequence '1..12', keep, eval='@*$n'} in a loop over $n kept the first $n's
  // multiples every time, and keep, chance=2 repeated one random pick.
  //
  // An expression of the value, numbers, strings and operators alone gives the same terms
  // every time, and they are kept for the request under its text, pqEvalKey (): kept for the
  // run only, {sequence '1..20', name='i'}{sequence '1..5', keep, eval='@*2'} made the 10,000
  // terms of '@*2' twenty times. A run still asks the expression for its first term, which is
  // where eval/loop.php checks where its text came from, and makes the terms again when that
  // one differs. The key is made once a run.

  if ( $pqSeq == 'eval' and is_string ( $pqParm ) and ! ( $GLOBALS ['padInfo'] ?? FALSE ) )
    $pqCheckKey = $pqCheckEval [$pqParm] ??= pqEvalKey ( $pqParm );
  else
    $pqCheckKey = '';

  if ( $pqCheckKey !== '' ) {

    $pqCheckTerms = &$padSeqCheckList;

    if ( isset ( $pqCheckTerms [$pqCheckKey] ) and ! isset ( $pqCheckFirst [$pqCheckKey] ) ) {
      $pqCheckFirst [$pqCheckKey] = TRUE;
      if ( pqTerms ( $pqSeq, $pqParm, [ 'to' => 1 ] ) !== $pqCheckTerms [$pqCheckKey] ['first'] )
        unset ( $pqCheckTerms [$pqCheckKey] );
    }

  } else {

    if ( pqTermsKey ( $pqSeq, '', $pqParm ) === '' )
      $pqCheckTerms = &$pqCheckRun;
    else
      $pqCheckTerms = &$padSeqCheckList;

    $pqCheckKey = $pqSeq . '|' . pqCheckParm ( $pqParm );

  }

  // How far: a draw - chance, random - is a term or not at its own position, so its terms are
  // made as far as the value, where the reach a run has by default made 10,000 draws for every
  // tag however short its list - a keep of five values with chance=2 took twenty times what it
  // took before. Any other type as far as the value and at least that reach, extended by
  // doubling; and past the reach toward a value beyond every term made only while the terms,
  // growing as they grow at the end of what was made, can get to it within the ceiling of
  // candidates (pqCheckReach): {sequence '9223372036854775807;4', flag, eval='@ * 2'} made a
  // million terms for its first value, which '@ * 2' reaches at position 4.6 * 10^18, and
  // spent the five seconds a run has under load, answering one flag of two; eval='abc' the same
  // for terms that are no numbers at all.
  //
  // That estimate decides only for a value past the ceiling, whose walk was the million terms:
  // a value within it is looked for as far as itself, as before, a walk its size bounds. Asked
  // for every value, the estimate turned members down - a line a * n + b with b above 0 is
  // projected past where it gets there, stairs wider than the reach stand still over what was
  // made, and an expression may turn after its first terms - so {sequence '15000;50000;100000',
  // keep, eval='@ + 1'} kept 15000 alone, ceil=20000 kept none of 40000 and 60000, and
  // eval='@ le 10000 ? @ * 3 : @ + 1' turned 20002 down.

  $pqCheckDefault = $GLOBALS ['padSeqDefaultTries'] ?? 10000;
  $pqCheckDraw    = in_array ( $pqSeq, [ 'chance', 'random' ] );
  $pqCheckMost    = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

  // An expression is held to no estimate of where its terms go: they go wherever the template
  // wrote, and how they went over the reach says nothing of what comes after it. A number past
  // every term made over the reach is looked for in the terms at twice, four times ... the
  // reach and at the ceiling, made one at a time and kept with the terms (pqCheckSamples): one
  // of them that is the value makes it a term, every one of them short of it on the side the
  // terms made are on - or, for terms that were no numbers, no number either - leaves it none,
  // and otherwise every term to the ceiling is made to look in. Estimated, {sequence
  // '2000000;3', flag, eval='@ le 10000 ? @ : 2000000'} answered 0 for the 2000000 the
  // expression makes at position 10,001 - its first 10,000 terms climb one at a time, which
  // gets there past the ceiling - and a value within the ceiling was looked for only as far as
  // itself, so eval='@ le 20000 ? 0 : 5' had no 5, made at position 20,001. Made to the
  // ceiling every time, the million terms of '@ * 2' for 9223372036854775807 took a second and
  // a half, and those of 'abc' the five seconds a run has.
  //
  // The terms to the ceiling are made a stretch at a time, looked in as they come, and kept with
  // where they got to (pqCheckWalk): a value made early is found there without the rest. Once
  // that walk has begun, every value is looked for in it, a number or not, going on to the
  // ceiling for one not found yet - as every value was looked for in the million terms the one
  // run to the ceiling left.

  if ( isset ( $pqCheckTerms [$pqCheckKey] ['walk'] ) )
    return pqCheckWalk ( $pqCheckTerms [$pqCheckKey], $pqSeq, $pqParm, $pqCheckAt, $pqCheckMost );

  if ( $pqSeq == 'eval' and is_numeric ( $pqLoop ) ) {

    if ( ( $pqCheckTerms [$pqCheckKey] ['to'] ?? 0 ) < $pqCheckDefault )
      $pqCheckTerms [$pqCheckKey] = pqTermsIndex ( $pqSeq, $pqParm, $pqCheckDefault );

    if ( isset ( $pqCheckTerms [$pqCheckKey] ['index'] [$pqCheckAt] ) )
      return TRUE;

    if ( $pqCheckTerms [$pqCheckKey] ['to'] < $pqCheckMost
         and ( $pqCheckTerms [$pqCheckKey] ['last'] === NULL
               or $pqLoop < $pqCheckTerms [$pqCheckKey] ['low'] or $pqLoop > $pqCheckTerms [$pqCheckKey] ['high'] ) ) {

      $pqCheckTerms [$pqCheckKey] ['samples'] ??= pqCheckSamples ( $pqSeq, $pqParm, $pqCheckTerms [$pqCheckKey] ['to'], $pqCheckMost );

      $pqCheckSeen = pqCheckSeen ( $pqCheckTerms [$pqCheckKey], $pqLoop, $pqCheckAt );

      if ( $pqCheckSeen !== NULL )
        return $pqCheckSeen;

      return pqCheckWalk ( $pqCheckTerms [$pqCheckKey], $pqSeq, $pqParm, $pqCheckAt, $pqCheckMost );

    }

  }

  $pqCheckFar     = is_numeric ( $pqLoop ) ? (int) min ( abs ( (float) $pqLoop ), $pqCheckMost ) : 0;
  $pqCheckWithin  = ( is_numeric ( $pqLoop ) and abs ( (float) $pqLoop ) <= $pqCheckMost );
  $pqCheckTo      = $pqCheckDraw ? $pqCheckFar : max ( $pqCheckFar, $pqCheckDefault );

  while ( ( $pqCheckTerms [$pqCheckKey] ['to'] ?? 0 ) < $pqCheckTo
          and ! isset ( $pqCheckTerms [$pqCheckKey] ['index'] [$pqCheckAt] ) ) {

    $pqCheckMade = $pqCheckTerms [$pqCheckKey] ['to'] ?? 0;

    if ( $pqCheckDraw or $pqCheckWithin )
      $pqCheckNext = $pqCheckTo;
    elseif ( $pqCheckMade < $pqCheckDefault )
      $pqCheckNext = $pqCheckDefault;
    elseif ( ! ( $pqCheckNext = pqCheckReach ( $pqCheckTerms [$pqCheckKey], $pqLoop, $pqCheckTo ) ) )
      break;

    $pqCheckTerms [$pqCheckKey] = pqTermsIndex ( $pqSeq, $pqParm, max ( $pqCheckNext, 2 * $pqCheckMade ) );

  }

  return isset ( $pqCheckTerms [$pqCheckKey] ['index'] [$pqCheckAt] );

?>
