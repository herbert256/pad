<?php

  // Calls a built-in pipe function from PAD/functions/, the handler for kind 'pad'.
  // The function file reads $value, $parm and $count from scope; its result is returned.
  //
  // The functions that cannot run without their parameters are held to them here: fewer
  // given is reported under the strict syntax check, and the lenient walk passes the
  // value through untouched - the one thing a pipe must never do is swallow it. Reading
  // a missing parameter was a PHP error before either could speak.

  $padFnNeeds = [ 'replace' => 2, 'between' => 2, 'range' => 2, 'mid' => 1, 'substr' => 1 ];

  if ( isset ( $padFnNeeds [$name] ) and $count < $padFnNeeds [$name] ) {

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      return padError ( "the function '$name' wants " . $padFnNeeds [$name]
                      . " parameter" . ( $padFnNeeds [$name] > 1 ? 's' : '' ) );

    return $value;

  }

  return include PAD . "functions/$name.php";

?>