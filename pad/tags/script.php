<?php

  $pExec = APP . "scripts/" . escapeshellcmd ($pParm[$p]);

  if ( ! file_exists($pExec) ) {
    pError ("Script $pExec not found");
    return FALSE;
  }

  $pExec_out = $pExec_args = [];

  foreach($pPrmsVal[$p] as $pK => $pV)
    if ($pK)
      $pExec_args [$pK] = escapeshellarg ($pV);

  $pExec_args = implode(" ", $pExec_args);
  
  exec ("$pExec $pExec_args", $pExec_out, $pExec_return);

  if ( $pExec_return ) {
    pError ("Script $pExec has returned error $pExec_return");
    return FALSE;    
  }

  return implode("\n", $pExec_out);
  
?>