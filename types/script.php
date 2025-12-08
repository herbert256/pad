<?php

  foreach ( glob ( padScriptCheck ( $padTag [$pad] ) ) as $padExec ) {

    $padExecOut = $padExecArgs = [];

    foreach($padOpt [$pad] as $padK => $padV)
      if ($padK)
        $padExecArgs [$padK] = escapeshellarg ($padV);

    $padExecArgs = implode(" ", $padExecArgs);
    
    exec ("$padExec $padExecArgs", $padExecOut, $padExecReturn);

    if ( $padExecReturn ) {
      padError ("Script $padExec has returned error $padExecReturn");
      return FALSE;    
    }

    return implode("\n", $padExecOut);

  }

?>