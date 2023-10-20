<?php

  $check = padAtSearch ( $GLOBALS['padSaveLvl'] [$i], $names );
  if ( $check !== INF ) 
    return $check;

  $check = padAtSearch ( $GLOBALS['padSaveOcc'] [$i], $names );
  if ( $check !== INF ) 
    return $check;

  return INF;

?>