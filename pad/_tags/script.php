<?php

  $padExec = padApp . "_scripts/" . escapeshellcmd ($padOpt [$pad] [1]);

  if ( ! padExists($padExec) ) {
    padError ("Script $padExec not found");
    return FALSE;
  }

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
  
?>