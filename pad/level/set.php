<?php

  if ( ! pValid ($pSet_name) )
    return pError ("{set} syntax error (1)");

  if ( $pTag[$p]<> 'set' or $pPair )
    if ( isset($GLOBALS [$pSet_name]) )
      $pSet_save[$p] [$pSet_name] = $GLOBALS [$pSet_name];
    else
      $pSet_delete[$p] [] = $pSet_name;

  $GLOBALS [$pSet_name] = pVar_opts ( '', pExplode($pSet_value, '|') );

  return TRUE;
  
?>