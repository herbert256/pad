<?php

  $padMakeType = $padTag [$pad];
  $padMakeFile = $padOpt [$pad] [1];

  $padData [$pad] = padData ("$padMakeFile.$padMakeType", $padMakeType, $padMakeFile);

  $padForceTagName = $padMakeFile;

  return TRUE;

?>