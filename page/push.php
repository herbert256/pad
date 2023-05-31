<?php
  
  $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] = [];

  foreach ($GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 3) == 'pad' and ! in_array($padK, $GLOBALS['padLevelVars']) 
         and $padK <> 'padFunctionSave' and $padK <> 'padK' and $padK <> 'padV' ) 
      $GLOBALS ['padFunctionSave'] [$GLOBALS['pad']] [$padK] = $GLOBALS [$padK];

?>