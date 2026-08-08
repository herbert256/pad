<?php

  // Implements toBool="name": records whether the level produced anything as a flag in
  // $padBoolStore - FALSE when the tag was null, took its else branch or left a blank result,
  // TRUE otherwise - and then blanks the text so the storing prints nothing.
  //
  // End-phase option; the flag is read back with {bool:name}. Works on $padContent, because
  // the walker copies the result in before the phase and back out after it - blanking
  // $padResult directly, as this used to, was silently undone.

  $padStoreName = $padPrm [$pad] ['toBool'];

  if     ( $padNull [$pad]  ) $padBoolStore [$padStoreName] = FALSE;
  elseif ( $padElse [$pad]  ) $padBoolStore [$padStoreName] = FALSE;
  elseif ( trim ( $padContent ) != '' ) $padBoolStore [$padStoreName] = TRUE;
  else                                  $padBoolStore [$padStoreName] = FALSE;

  $padContent = '';

?>