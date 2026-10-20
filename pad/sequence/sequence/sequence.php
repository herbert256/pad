<?php

  // Runs a sequence type from inside an expression and returns one term of it.
  //
  // Reached from start/eval/sequence.php when the evaluator meets sequence:<name>(n) and the name
  // matches a directory in types/. inits/direct.php imports the engine globals (the whole chain
  // runs inside a function) and inits/eval.php reads the call: the name is the type, the argument
  // is which term is wanted.
  //
  // The steps are the tag path's, minus the ones that only make sense for a tag. There is no
  // level to publish to, no store to update and no data to shape, so exits/exits.php is not used
  // and only the type's own exit hook and the actions run.
  //
  // What comes back is the term itself, not a list of one: sequence:fibonacci(8) is a way of
  // asking for the eighth Fibonacci number, and 13 is the answer to that. The action half of the
  // prefix - sequence:sum([1,2,3]), which sequence/action.php serves - answers with an array
  // instead, because what an action produces is a list.

  include PQ . 'inits/direct.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'inits/eval.php';
  include PQ . 'build/inits.php';

  // The position is the run's length, whatever the type's init.php makes of its parameter -
  // loop reads it as a row count, and sequence:loop(3, 5) answered the fifth term.

  $pqPositionRows = $pqRows;

  include PQ . 'inits/init.php';

  $pqRows = $pqPositionRows;

  include PQ . 'plays/inits.php';
  include PQ . 'inits/limits.php';
  include PQ . 'actions/inits.php';

  // A type with a formula of its position - function.php - answers every position from the
  // formula, as {sequence square, from=500000, rows=1} does: it is the term the walk makes
  // there, square's loop.php makes its terms through it, and prime's is the nth of the values
  // its test accepts. Taken past the ceiling of candidates only, a walk answered below it, a
  // term at a time through the build, which the five seconds a run has cut off from some
  // 60,000 terms on - sequence:square(500000) and sequence:totient(300000) were "cannot be
  // reached" where sequence:square(1000001) answered at once - and prime's walk reached the
  // 78498 primes of its million candidates, so sequence:prime(100000) was unreachable too
  // where the formula answers 1299709.

  if ( $pqRows >= 1 and padFileExists ( PT . "$pqSeq/function.php" ) ) {

    include PQ . 'build/vars.php';
    include PQ . 'build/include.php';

    $pqTerm = ( 'pq' . ucfirst ( $pqSeq ) ) ( $pqRows );

    // A position the formula has no term for is named with the reason: a type made in order
    // only as far as the ceiling a run walks to answers past it as an overflow does, and
    // sequence:prime(2000000) said "the type makes no term there inside the integer range" of
    // the 2,000,000th prime, 32452843.

    if ( $pqTerm === FALSE or ( is_float ( $pqTerm ) and ( is_nan ( $pqTerm ) or $pqTerm <= PHP_INT_MIN or $pqTerm >= PHP_INT_MAX ) ) ) {

      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "sequence:$pqSeq($pqRows) cannot be reached: "
                 . ( ( $pqTerm === FALSE ) ? "the type has no term there"
                 : ( ( $pqRows > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) and pqCeilingType ( $pqSeq ) )
                     ? "the type is made no further than position " . ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) . ", the ceiling a run walks to"
                     : "the type makes no term there inside the integer range" ) ) );

      return '';

    }

    include PQ . 'exits/info.php';

    return $pqTerm;

  }

  // The terms a position lookup made are kept for the request, per type and parameter, so a
  // later lookup at or below them is answered from them: made afresh for every lookup, one
  // per row was quadratic - {sequence '1..400'}{echo sequence:happy($sequence)}{/sequence}
  // walked from 1 four hundred times. Not for a type that draws at random or evaluates an
  // expression, a parameter drawn per term or naming a store, nor under {info}, which records
  // every run (pqTermsKey in lib/sequence.php).
  //
  // A walk that came up short of what it was asked - the type ends, or the candidates or the
  // time a run has ran out - is as far as the request reaches, and is kept with the reason: a
  // lookup past it is named at once, where it walked to the ceiling or the clock again on
  // every call, {sequence '1..8'}{echo sequence:strong($sequence)} {/sequence} five seconds a
  // row under the lenient walk. A lookup past the terms kept goes on from the candidate after
  // the last one when the type tests the values it walks - a bool type, strong, happy - for
  // the terms missing only, with what is left of the candidates; any other walk is made again
  // for twice as many. Made again for twice as many, the walk of strong, which has four terms,
  // ran to the ceiling for six: sequence:strong(4) after sequence:strong(3) took 23 times what
  // it takes alone.

  $pqTermsKey   = ( $pqRows >= 1 ) ? pqTermsKey ( $pqSeq, $pqBuild, $pqParm ) : '';
  $pqTermsAsked = $pqRows;
  $pqTermsTry   = $pqTry;
  $pqTermsKept  = [];
  $pqTermsTries = 0;

  if ( $pqTermsKey !== '' ) {

    $pqTermsMade = pqTermsMade ( $pqTermsKey );

    if ( count ( $pqTermsMade ['terms'] ) >= $pqRows )
      return $pqTermsMade ['terms'] [ $pqRows - 1 ];

    if ( $pqTermsMade ['end'] !== '' ) {
      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "sequence:$pqSeq($pqRows) cannot be reached: the sequence gives "
                 . count ( $pqTermsMade ['terms'] ) . " terms within " . $pqTermsMade ['end'] );
      return '';
    }

    if ( $pqTermsMade ['next'] !== NULL ) {
      $pqTermsKept  = $pqTermsMade ['terms'];
      $pqTermsTries = $pqTermsMade ['tries'];
      $pqFrom       = $pqTermsMade ['next'];
      $pqTry        = max ( 0, $pqTry - $pqTermsTries );
      $pqRows       = $pqRows - count ( $pqTermsKept );
    } else
      $pqRows = max ( $pqRows, 2 * count ( $pqTermsMade ['terms'] ) );

  }

  include PQ . 'build/build.php';

  // Within what a walk came up short: the time a run has - build/one.php marks it - the
  // candidates it may test, or the range of a type that ends. The clock was reported as the
  // candidates: under load sequence:strong(5) gave "3 terms within 1000000 candidates" after
  // five seconds.

  $pqTermsEnd = ( count ( $pqResult ) >= $pqRows ) ? ''
              : ( ( ( $pqTimeOut ?? FALSE ) ? "the " . ( $GLOBALS ['padSeqMaxSeconds'] ?? 5 ) . " seconds a run has"
              : ( $pqTriesOut ? "$pqTermsTry candidates" : "its range" ) ) );

  $pqResult = array_merge ( $pqTermsKept, array_values ( $pqResult ) );

  if ( $pqTermsKey !== '' )
    pqTermsMade ( $pqTermsKey, [
      'terms' => $pqResult,
      'end'   => $pqTermsEnd,
      'next'  => ( $pqTermsEnd === '' and $pqBuild == 'bool' ) ? $pqLoop + $pqInc : NULL,
      'tries' => $pqTermsTries + $pqTries
    ] );

  $pqRows = $pqTermsAsked;

  // The term asked for, or nothing: answering the last term found gave sequence:prime(1230)
  // as 9973, the 1229th prime, and sequence:fibonacci(100) as the 93rd term, the last that
  // fits an integer. Strict mode says which.

  if ( ! $pqRows )
    return '';

  if ( count ( $pqResult ) < $pqRows ) {

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "sequence:$pqSeq($pqRows) cannot be reached: the sequence gives " . count ( $pqResult )
               . " terms within $pqTermsEnd" );

    return '';

  }

  include PQ . 'exits/exit.php';
  include PQ . 'exits/actions.php';
  include PQ . 'exits/done.php';
  include PQ . 'exits/info.php';

  $pqEvalResult = array_values ( $pqResult );

  return $pqEvalResult [ $pqRows - 1 ];

?>
