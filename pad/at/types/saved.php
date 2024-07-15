<?php

  $check = padAtSearch ( $GLOBALS['padSaveLvl'] [$padIdx], $names );
  if ( $check !== INF ) 
    return $check;

  $check = padAtSearch ( $GLOBALS['padSaveOcc'] [$padIdx], $names );
  if ( $check !== INF ) 
    return $check;

  return INF;

?>