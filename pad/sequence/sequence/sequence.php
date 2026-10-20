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

  // The terms a position lookup made are kept for the request, per type and parameter, so a
  // later lookup at or below them is answered from them, and one past them makes twice as
  // many: made afresh for every lookup, one per row was quadratic - {sequence '1..400'}{echo
  // sequence:prime($sequence)}{/sequence} took 27 seconds. Not for a type that draws at
  // random or evaluates an expression, a parameter drawn per term or naming a store, nor
  // under {info}, which records every run (pqTermsKey in lib/sequence.php).

  // A position past the ceiling of candidates no walk reaches; a type with a formula of its
  // position - function.php - answers it from the formula, as {sequence square,
  // from=2000000, rows=1} does: sequence:square(2000000) was "cannot be reached". Within the
  // ceiling the walk answers, as the type is built.

  if ( $pqRows > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) and padFileExists ( PT . "$pqSeq/function.php" ) ) {

    include PQ . 'build/include.php';

    $pqTerm = ( 'pq' . ucfirst ( $pqSeq ) ) ( $pqRows );

    if ( $pqTerm === FALSE or ( is_float ( $pqTerm ) and ( is_nan ( $pqTerm ) or $pqTerm <= PHP_INT_MIN or $pqTerm >= PHP_INT_MAX ) ) ) {
      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "sequence:$pqSeq($pqRows) cannot be reached: the type makes no term there inside the integer range" );
      return '';
    }

    return $pqTerm;

  }

  $pqTermsKey   = ( $pqRows >= 1 ) ? pqTermsKey ( $pqSeq, $pqBuild, $pqParm ) : '';
  $pqTermsAsked = $pqRows;

  if ( $pqTermsKey !== '' ) {

    $pqTermsMade = pqTermsMade ( $pqTermsKey );

    if ( count ( $pqTermsMade ) >= $pqRows )
      return $pqTermsMade [ $pqRows - 1 ];

    $pqRows = max ( $pqRows, 2 * count ( $pqTermsMade ) );

  }

  include PQ . 'build/build.php';

  if ( $pqTermsKey !== '' ) {
    pqTermsMade ( $pqTermsKey, array_values ( $pqResult ) );
    $pqRows = $pqTermsAsked;
  }

  // The term asked for, or nothing: answering the last term found gave sequence:prime(1230)
  // as 9973, the 1229th prime, and sequence:fibonacci(100) as the 93rd term, the last that
  // fits an integer. Strict mode says which.

  if ( ! $pqRows )
    return '';

  if ( count ( $pqResult ) < $pqRows ) {

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "sequence:$pqSeq($pqRows) cannot be reached: the sequence gives "
               . count ( $pqResult ) . " terms within " . ( $pqTriesOut ? "$pqTry candidates" : "its range" ) );

    return '';

  }
  include PQ . 'exits/exit.php';
  include PQ . 'exits/actions.php';
  include PQ . 'exits/done.php';
  include PQ . 'exits/info.php';

  $pqEvalResult = array_values ( $pqResult );

  return $pqEvalResult [ $pqRows - 1 ];

?>
