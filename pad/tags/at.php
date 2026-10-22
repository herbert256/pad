<?php

  // {at "country.id='f0_325'@mondial"} looks a value up in a data set or sequence by way
  // of an @ expression, and returns what it points at. The at/ subsystem does the
  // searching.

  // The explicit {at} spelling asserts its path, and a miss answered a quiet empty.
  // Strict mode names the path; the expression forms keep their deep-search leniency,
  // which the optional idiom rests on.

  // The answer has a name of its own: kept in $padAtTag it overwrote the engine's per-level
  // array of that name - the mark a property tag leaves on its level - and the next tag to
  // open a level ended the request on "Cannot assign an empty string to a string offset".

  $padAtAnswer = padAtValue ( $padParm );

  if ( $padAtAnswer === padMiss or $padAtAnswer === '' or $padAtAnswer === NULL ) {

    // The marker of no answer can sit in $padAtAnswer here, and the error dump cannot json
    // it - the report shipped as an empty 500, when it was INF, until it was emptied first.

    $padAtAnswer = '';

    if ( $padCheckSyntax )
      padError ( "the path '$padParm' reaches nothing for the at tag" );

    return '';

  }

  return $padAtAnswer;

?>
