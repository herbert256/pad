<?php

  if ( isset ( $GLOBALS ['padStrCod'] ) )
    unset ( $GLOBALS ['padStrCod'] );

  $GLOBALS ['padStrFun'] = TRUE;

  if ( ! isset ( $GLOBALS ['padInFunction'] ) ) 
    $GLOBALS ['padInFunction'] = 1;
  else
    $GLOBALS ['padInFunction']++;

  $padInFunctionResult =  include pad . 'start/pad.php'; 

  $GLOBALS ['padInFunction']--;

  if ( ! $GLOBALS ['padInFunction'] )
    unset ( $GLOBALS ['padInFunction'] );  

  return $padInFunctionResult;

?>