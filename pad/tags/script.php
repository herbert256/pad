<?php

  $padExec = APP . "scripts/" . escapeshellcmd ($padPrm [$pad] [1]);

  if ( ! file_exists($padExec) ) {
    padError ("Script $padExec not found");
    return FALSE;
  }

  $padExecOut = $padExecArgs = [];

  foreach($padPrm [$pad] as $padK => $padV)
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