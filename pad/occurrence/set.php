<?php

  // Publishes the current row as occurrence variables, so {$field} resolves inside the
  // loop body.
  //
  // The tag parameter is published under the tag's own name first, then the level's %var
  // assignments ($padSetOcc) are evaluated into the row, then every field is set through
  // padSetGlobalOcc - which saves whatever it shadows so padResetOcc can put it back when
  // the occurrence ends.

  $padSetTmp = [];

  // A parameter of 0 is a parameter: tested for truth, {stars 0} published nothing, and the
  // {$stars} inside it failed under the strict check as a field that is not there.

  if ( $padParm or is_numeric ( $padParm ) )
    $padSetTmp [ $padName [$pad] ] = $padParm;

  foreach ( $padSetOcc [$pad] as $padK => $padV )
    $padCurrent [$pad] [$padK] = padEval ( $padV );

  foreach ( $padCurrent [$pad] as $padK => $padV )
    $padSetTmp [$padK] = $padV;

  // Which of the names are globals already is found for the row at once (lib/scope.php):
  // the row's few names looked up in the symbol table, not its some 450 entries in the row.

  $padSetHave = $padSetTmp ? array_intersect_key ( $padSetTmp, $GLOBALS ) : [];

  foreach ( $padSetTmp as $padK => $padV )
    padSetGlobalOcc ( $padK, $padV, array_key_exists ( $padK, $padSetHave ) );

  unset ( $padSetHave );

?>
