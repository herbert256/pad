<?php

  $padMakeType = $padTag [$pad];
  $padMakeFile = $padParm;

  $padData [$pad] = padData ("$padMakeFile.$padMakeType", $padMakeType, $padMakeFile);

  $padForceTagName = $padMakeFile;

  return TRUE;

?>