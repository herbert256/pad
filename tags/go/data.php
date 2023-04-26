<?php

  $padMakeType = $padTag [$pad];
  $padMakeFile = $padOpt [$pad] [1];

  $padData [$pad] = padMakeData ("$padMakeFile.$padMakeType", $padMakeType, $padMakeFile);

  $padForceName = $padMakeFile;

  return TRUE;

?>