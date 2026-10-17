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

  if ( ! file_exists ( PAD . "functions/$name.php" ) ) {

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
  // other names: as text a moment of another zone was read in the application's zone and
  // came out hours off.

  if ( ! in_array ( $name, [ 'date', 'time', 'timestamp', 'ago', 'localDate' ], TRUE ) )
    $value = padDateText ( $value );

  $padFnNeeds = [ 'replace' => 2, 'between'    => 2, 'range'    => 2,
                  'mid'     => 1, 'substr'     => 1, 'left'     => 1,
                  'right'   => 1, 'max_len'    => 1, 'like'     => 1,
                  'after'   => 1, 'afterLast'  => 1, 'contains' => 1,
                  'before'  => 1, 'beforeLast' => 1, 'cut'      => 1 ];

  if ( isset ( $padFnNeeds [$name] ) and $count < $padFnNeeds [$name] ) {

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "the function '$name' wants " . $padFnNeeds [$name]
                      . " parameter" . ( $padFnNeeds [$name] > 1 ? 's' : '' ) );

    return $value;

  }

  return include PAD . "functions/$name.php";

?>
