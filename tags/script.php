<?php

  $pad_exec = PAD_APP . "scripts/" . escapeshellcmd ($pad_parm);

  if ( ! pad_file_exists($pad_exec) ) {
    pad_error ("Script $pad_exec not found");
    return FALSE;
  }

  $pad_exec_out = $pad_exec_args = [];

  foreach($pad_parms_val as $pad_k => $pad_v)
    if ($pad_k)
      $pad_exec_args [$pad_k] = escapeshellarg ($pad_v);

  $pad_exec_args = implode(" ", $pad_exec_args);
  
  exec ("$pad_exec $pad_exec_args", $pad_exec_out, $pad_exec_return);

  if ( $pad_exec_return ) {
    pad_error ("Script $pad_exec has returned error $pad_exec_return");
    return FALSE;    
  }

  return implode("\n", $pad_exec_out);
  
?>