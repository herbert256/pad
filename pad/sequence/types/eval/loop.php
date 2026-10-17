<?php

  // Build strategy 'loop' for the eval sequence: the term is the parameter evaluated as a
  // PAD expression with the loop value substituted for @, so {eval '@ * @ + 1'} gives 2, 5,
  // 10, 17, ... The general escape hatch for a formula that has no sequence type of its
  // own; as a play, {make eval='@ * 2'} rewrites another sequence's terms.
  //
  // The expression is the template's own text, as where= is (handling/types/where.php): one
  // of the tag's items written as a quoted string - eval='@ * 2', {eval '@ + 1'},
  // make='eval|@ * 2', sequence:eval(3, '@ * 2') in an expression (eval/parms/sequence.php).
  // From a value - eval=$q, {eval $q}, make=$q holding 'eval|...' - a request value with
  // php:php_uname('s') in it ran PHP. A value goes into it as $name.

  $pqEvalLevel = $GLOBALS ['pad'];

  if ( $GLOBALS ['padProtectValues'] and is_string ( $pqParm ) and ! is_numeric ( $pqParm ) ) {

    if ( ! isset ( $GLOBALS ['pqEvalLiterals'] [$pqEvalLevel] [$pqParm] ) ) {

      $pqEvalWritten = FALSE;

      foreach ( $GLOBALS ['padParms'] [$pqEvalLevel] ?? [] as $pqEvalOne ) {
        $pqEvalText = (string) ( $pqEvalOne ['padPrmOrg'] ?? '' );
        if ( ( $pqEvalOne ['padPrmKind'] ?? '' ) == 'option' and str_contains ( $pqEvalText, '=' ) )
          $pqEvalText = substr ( $pqEvalText, strpos ( $pqEvalText, '=' ) + 1 );
        $pqEvalText = padMetaLiteral ( $pqEvalText );
        if ( is_string ( $pqEvalText ) and ( $pqEvalText === $pqParm or ( explode ( '|', $pqEvalText, 2 ) [1] ?? NULL ) === $pqParm ) )
          $pqEvalWritten = TRUE;
      }

      $GLOBALS ['pqEvalLiterals'] [$pqEvalLevel] [$pqParm] = $pqEvalWritten;

    }

    if ( ! $GLOBALS ['pqEvalLiterals'] [$pqEvalLevel] [$pqParm] and ! isset ( $GLOBALS ['pqEvalArguments'] [$pqParm] ) ) {
      padError ( "eval= is an expression the template writes: give it as a quoted string, and put a value in it as \$name" );
      return '';
    }

  }

  // A value spliced into the expression - kept by padSpliceQuote (lib/level.php) - stands in
  // it only as a string of it: outside the expression's own strings it is part of the code,
  // the request value's php:abs(-3) called, in any spelling a guard there did not know.

  $pqEvalKey = count ( $GLOBALS ['padSpliceValues'] ?? [] ) . ':' . $pqParm;

  if ( is_string ( $pqParm ) and ! isset ( $GLOBALS ['pqEvalSpliced'] [$pqEvalKey] ) ) {

    $pqEvalCode = preg_replace ( '/\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"/s', '', $pqParm );

    $GLOBALS ['pqEvalSpliced'] [$pqEvalKey] = FALSE;

    foreach ( array_keys ( $GLOBALS ['padSpliceValues'] ?? [] ) as $pqEvalOne )
      if ( str_contains ( $pqEvalCode, (string) $pqEvalOne ) )
        $GLOBALS ['pqEvalSpliced'] [$pqEvalKey] = TRUE;

  }

  if ( $GLOBALS ['pqEvalSpliced'] [$pqEvalKey] ?? FALSE ) {
    padError ( "a value spliced into the expression of the eval sequence is part of its code: put it in as \$name" );
    return '';
  }

  return padEval ( $pqParm, $pqLoop );

?>
