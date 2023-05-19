<?php

  $padMakeType = $padOptionName;
  $padMakeFile = $padPrm [$pad] [$padOptionName];

  $padData [$pad] = padData ("$padMakeFile.$padMakeType", $padMakeType, $padMakeFile);

  $padForceName = $padMakeFile;

  return TRUE;

?>