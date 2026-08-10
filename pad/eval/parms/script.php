<?php

  // Runs a script named with the script: prefix inside an expression - the missing half of
  // types/script.php, whose absence ended the request on a bare include the moment a name
  // in an expression resolved to the script type (found when a ci script briefly lived in
  // an application's _scripts/ directory). The call's arguments are passed escaped; with
  // none written the piped value is the one argument, and the script's stdout is the answer.

  foreach ( glob ( padScriptCheck ( $name ) ) as $padExec ) {

    $padExecOut  = [];
    $padExecArgs = [];

    if ( ! $count and $value !== '' )
      $parm = [ $value ];

    foreach ( $parm as $padV )
      $padExecArgs [] = escapeshellarg ( $padV );

    $padExecArgs = implode ( ' ', $padExecArgs );

    exec ( "$padExec $padExecArgs", $padExecOut, $padExecReturn );

    if ( $padExecReturn )
      return padError ( "Script $padExec has returned error $padExecReturn" );

    return implode ( "\n", $padExecOut );

  }

  global $padCheckSyntax;

  return ( $padCheckSyntax ) ? padError ( "Script '$name' was not found in a _scripts directory" ) : '';

?>