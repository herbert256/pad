<?php

  // Calls a built-in pipe function from PAD/functions/, the handler for kind 'pad'.
  // The function file reads $value, $parm and $count from scope; its result is returned.
  //
  // The functions that cannot run without their parameters are held to them here: fewer
  // given is reported under the strict syntax check, and the lenient walk passes the
  // value through untouched - the one thing a pipe must never do is swallow it. Reading
  // a missing parameter was a PHP error before either could speak.

  // The pad: prefix names the kind outright - {echo $x | pad:upper} - and a name with no
  // file here went to the include as it was: {echo $x | pad:strtoupper} ended the request
  // on a PHP warning in either mode. Named under the strict check, empty in the lenient
  // walk, as an unknown pipe function is.

  if ( ! padFunctionBuiltIn ( $name ) ) {

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "there is no PAD function named '$name'" );

    return '';

  }

  // A NULL handed along the chain - parse_url without a query, a function with no answer -
  // is the empty value it is everywhere else in PAD; the function files hand $value to
  // PHP's string functions, and {$u | php:parse_url(@, PHP_URL_QUERY) | trim} ended the
  // request on "Passing null to parameter #1 is deprecated". A NULL field never got here, it
  // is read as ''; default, optional and ago take '' as they take NULL.

  if ( $value === NULL )
    $value = '';

  // A date object is its text to a function that takes text (padDateText); date, ago and
  // localDate take the moment itself, and so do time and timestamp, the date function under
  // other names, and default and optional, which hand it on: as text a moment of another
  // zone was read in the application's zone and came out hours off - and so it did for Time
  // and DATE, which a file system that ignores case finds as well. A test of a date object against date objects - $d in ($e), $d range
  // ($e, $f) - reads those as text too, in the zone of the value, so one moment is one text:
  // they stayed objects, and a text never equalled or lay between them.

  if ( ! in_array ( strtolower ( $name ), [ 'date', 'time', 'timestamp', 'ago', 'localdate', 'default', 'optional' ], TRUE ) ) {

    if ( $value instanceof DateTimeInterface and is_array ( $parm ) )
      foreach ( $parm as $padDateKey => $padDateOne )
        if ( $padDateOne instanceof DateTimeInterface )
          $parm [$padDateKey] = DateTime::createFromInterface ( $padDateOne )->setTimezone ( $value->getTimezone () )->format ( 'Y-m-d H:i:s' );

    $value = padDateText ( $value );

  }

  $padFnNeeds = [ 'replace' => 2, 'between'    => 2, 'range'    => 2,
                  'mid'     => 1, 'substr'     => 1, 'left'     => 1,
                  'right'   => 1, 'max_len'    => 1, 'like'     => 1,
                  'after'   => 1, 'afterLast'  => 1, 'contains' => 1,
                  'before'  => 1, 'beforeLast' => 1, 'cut'      => 1 ];

  if ( isset ( $padFnNeeds [$name] ) and $count < $padFnNeeds [$name] ) {

    global $padCheckSyntax;

    // Written as a call with nothing piped in, its first argument was the value it works on
    // (padEvalCallNode) - and the count is of the arguments after it, which the message
    // says: replace('a', 'b') was told it wants 2 parameters, and had written two.

    if ( $padCheckSyntax )
      padError ( "the function '$name' wants " . $padFnNeeds [$name]
                      . " parameter" . ( $padFnNeeds [$name] > 1 ? 's' : '' )
                      . ( ( $padEvalShifted ?? FALSE ) ? " after the value it works on - written as a call its first argument is that value: $name(\$value, ...)" : '' ) );

    return $value;

  }

  return include PAD . "functions/$name.php";

?>
