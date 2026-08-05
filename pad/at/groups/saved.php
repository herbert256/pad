<?php

  // The saved group: look the path $names up in the globals level $padIdx shadowed - the
  // values padSetGlobalOcc() and padSetGlobalLvl() put aside before overwriting them, and
  // that padResetOcc() / padResetLvl() will restore. Occurrence scope is tried first.

  global $padSaveLvl, $padSaveOcc;

  $current = padAtSearch ( $padSaveOcc [$padIdx], $names );
  if ( $current !== INF )
    return $current;

  $current = padAtSearch ( $padSaveLvl [$padIdx], $names );
  if ( $current !== INF )
    return $current;

  return INF;

?>