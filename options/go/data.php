<?php

  $padMakeType = $padOptionName;
  $padMakeFile = $padPrm [$pad] [$padOptionName];

  $padData [$pad] = padMakeData ("$padMakeFile.$padMakeType", $padMakeType, $padMakeFile);

  $padForceName = $padMakeFile;

  return TRUE;

?>