<?php

  if ( ! pad_valid ($pSet_name) )
    return pError ("{set} syntax error (1)");

  if ( $pTag <> 'set' or $pPair )
    if ( isset($GLOBALS [$pSet_name]) )
      $pSet_save [$p] [$pSet_name] = $GLOBALS [$pSet_name];
    else
      $pSet_delete [$p] [] = $pSet_name;

  $GLOBALS [$pSet_name] = pad_var_opts ( '', pExplode($pSet_value, '|') );

  return TRUE;
  
?>