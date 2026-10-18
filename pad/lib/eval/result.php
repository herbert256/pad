<?php

  // Stage four of the evaluator: reduces the tokens of one pipe segment to a single value
  // and returns it. $value is the segment's input (the previous pipe's result, or the
  // caller's starting value) and is threaded through as $myself so that @ and operators
  // with a missing operand can reach it; $eval is the original text, kept only for error
  // messages.
  //
  // Order matters: inject the input value, collapse [ ] arrays, collapse ( ) groups, apply
  // operators by precedence, then concatenate whatever adjacent literals are left. What
  // survives must be exactly one VAL token, otherwise the expression was malformed and
  // padError says so.

  function padEvalResult ( $result, $value, $eval ) {

    // A pass runs when the segment holds what it works on - an @ or a format, a [, a (,
    // values left side by side - which one look at the kinds tells; each pass read every
    // token of every segment to find out.

    $kinds = array_flip ( array_column ( $result, 1 ) );

    if ( isset ( $kinds ['$$'] ) or isset ( $kinds ['%'] ) ) padEvalValue  ( $result, $value );
                                                              padEvalTrace  ( 'value1', $result );
    if ( isset ( $kinds ['a-open'] ) )                         padEvalArray  ( $result, $value );
                                                              padEvalTrace  ( 'array1', $result );
    if ( isset ( $kinds ['open'] ) )                           padEvalOpnCls ( $result, $value );
                                                              padEvalTrace  ( 'opncls1', $result );
                                                              padEvalOpr    ( $result, $value );
                                                              padEvalTrace  ( 'opr3', $result );
    if ( count ( $result ) > 1 )                               padEvalMulti  ( $result );
                                                              padEvalTrace  ( 'multi1', $result );

    $key = array_key_first ($result);

    // A shape that is not one value is reported under the strict syntax check; the
    // lenient answer for what cannot be evaluated is ''.

    if ( count ($result) != 1 or $result[$key][1] != 'VAL' ) {

      global $padCheckSyntax;

      if ( ! $padCheckSyntax )
        return '';

      // Values side by side are joined as text (padEvalMulti), so those left over hold a
      // list, which joins nothing - named as that, where it was "More than one result back";
      // and an empty ( ) as holding nothing, where it was "No result back".

      $padEvalVals = count ( array_filter ( $result, fn ( $one ) => $one [1] == 'VAL' ) );

      if     ( count($result) < 1        ) padError ("Expression error: there is no value in '" . trim ( $eval ) . "'");
      elseif ( count($result) > 1 and $padEvalVals == count ( $result ) )
                                           padError ("Expression error: a list and another value stand side by side with no operator between them: $eval");
      elseif ( count($result) > 1        ) padError ("More than one result back: $eval");
      elseif ( $result[$key][1] != 'VAL' ) padError ("Result is not a value: $eval");

      // Under an action that continues - log, ignore - the walk gets the lenient answer
      // too, not whatever token happened to come first, or a null key when none did.

      return '';

    }

    return $result [$key] [0];

  }

?>
