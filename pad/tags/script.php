<?php

  $padExec = "/app/_scripts/" . escapeshellcmd ($padParm);

  if ( ! file_exists($padExec) ) {
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