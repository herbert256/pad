<?php

  // {at "country.id='f0_325'@mondial"} looks a value up in a data set or sequence by way
  // of an @ expression, and returns what it points at. The at/ subsystem does the
  // searching.

  // The explicit {at} spelling asserts its path, and a miss answered a quiet empty.
  // Strict mode names the path; the expression forms keep their deep-search leniency,
  // which the optional idiom rests on.

  $padAtTag = padAtValue ( $padParm );

  if ( $padAtTag === INF or $padAtTag === '' or $padAtTag === NULL ) {

    // INF can sit in $padAtTag here, and the error dump cannot json a float infinity -
    // the report shipped as an empty 500 until it was emptied first.

    $padAtTag = '';

    if ( $padCheckSyntax )
      return padError ( "the path '$padParm' reaches nothing for the at tag" );

    return '';

  }

  return $padAtTag;

?>