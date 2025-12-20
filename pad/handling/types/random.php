<?php

  $padHandRandKeys       = array_keys ( $padData [$pad] );
  $padHandRandCount      = $padPrm [$pad] ['random']      ?? 0;
  $padHandRandOrderly    = $padPrm [$pad] ['orderly']     ?? 0;
  $padHandRandDuplicates = $padPrm [$pad] ['duplicates']  ?? 0;

  $padHandRandKeys = pqRandom ( $padHandRandKeys, $padHandRandCount, $padHandRandOrderly, $padHandRandDuplicates );

  $padHandRand    = $padData [$pad];
  $padData [$pad] = [];

  foreach ( $padHandRandKeys as $padK )
    $padData [$pad] [] = $padHandRand [$padK];

?>
