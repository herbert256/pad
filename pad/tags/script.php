<?php

  $padExec = APP . "scripts/" . escapeshellcmd ($padPrm [$pad]);

  if ( ! file_exists($padExec) ) {
    pError ("Script $padExec not found");
    return FALSE;
  }

  $padExec_out = $padExec_args = [];

  foreach($padPrmsVal [$pad] as $padK => $padV)
    if ($padK)
      $padExec_args [$padK] = escapeshellarg ($padV);

  $padExec_args = implode(" ", $padExec_args);
  
  exec ("$padExec $padExec_args", $padExec_out, $padExec_return);

  if ( $padExec_return ) {
    pError ("Script $padExec has returned error $padExec_return");
    return FALSE;    
  }

  return implode("\n", $padExec_out);
  
?>