<?php

  // Runs a script named with the script: prefix inside an expression - the missing half of
  // types/script.php, whose absence ended the request on a bare include the moment a name
  // in an expression resolved to the script type (found when a ci script briefly lived in
  // an application's _scripts/ directory). The call's arguments are passed escaped; with
  // none written the piped value is the one argument, and the script's stdout is the answer.
  //
  // The path padScriptCheck answers is run as it is, as types/script.php runs it: put
  // through glob() a bracketed route directory - blog/[year]/_scripts/ - was a character
  // class, nothing matched, and {echo 'p' | script:yr} was "not found" on the page where
  // {script:yr 'p'} ran the script.

  $padExec = padScriptCheck ( $name );

  if ( $padExec and is_file ( $padExec ) ) {

    $padExecOut  = [];
    $padExecArgs = [];

    if ( ! $count and $value !== '' )
      $parm = [ $value ];

    foreach ( $parm as $padV )
      $padExecArgs [] = escapeshellarg ( $padV );

    $padExecArgs = implode ( ' ', $padExecArgs );

    exec ( escapeshellarg ( $padExec ) . " $padExecArgs", $padExecOut, $padExecReturn );

    if ( $padExecReturn )
      return padError ( "Script $padExec has returned error $padExecReturn" );

    return implode ( "\n", $padExecOut );

  }

  global $padCheckSyntax;

  return ( $padCheckSyntax ) ? padError ( "Script '$name' was not found in a _scripts directory" ) : '';

?>
