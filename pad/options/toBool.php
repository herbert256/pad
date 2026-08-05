<?php

  // Implements toBool="name": records whether the level produced anything as a flag in
  // $padBoolStore - FALSE when the tag was null, took its else branch or left a blank result,
  // TRUE otherwise - and then blanks $padResult [$pad] so the storing prints nothing.
  //
  // End-phase option; the flag is read back with {bool:name}.

  $padStoreName = $padPrm [$pad] ['toBool'];

  if     ( $padNull [$pad]  ) $padBoolStore [$padStoreName] = FALSE;
  elseif ( $padElse [$pad]  ) $padBoolStore [$padStoreName] = FALSE;
  elseif ( trim ( $padResult [$pad] ) != '' ) $padBoolStore [$padStoreName] = TRUE;
  else                                             $padBoolStore [$padStoreName] = FALSE;

  $padResult [$pad] = '';

?>