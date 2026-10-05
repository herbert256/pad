<?php

  // Type handler for a shell script ({script:backup}): runs the script found in the nearest
  // _scripts/ directory and returns its stdout as the tag's value.
  //
  // padScriptCheck() finds the script by its whole name, the extension may be left off; the
  // script path and every tag parameter go to the shell through escapeshellarg(). A non-zero exit status
  // raises a PAD error and returns FALSE. Only the first match is ever run - the loop body
  // returns.

  foreach ( glob ( padScriptCheck ( $padTag [$pad] ) ) as $padExec ) {

    $padExecOut = $padExecArgs = [];

    foreach($padOpt [$pad] as $padK => $padV)
      if ($padK)
        $padExecArgs [$padK] = escapeshellarg ($padV);

    $padExecArgs = implode(" ", $padExecArgs);

    exec ( escapeshellarg ( $padExec ) . " $padExecArgs", $padExecOut, $padExecReturn );

    if ( $padExecReturn ) {
      padError ("Script $padExec has returned error $padExecReturn");
      return FALSE;
    }

    return implode("\n", $padExecOut);

  }

?>