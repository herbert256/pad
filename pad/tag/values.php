<?php

  $padReturn = [];

  foreach ($GLOBALS ['padCurrent'] [$padIdx] as $padK => $padV)
    $padReturn [] ['value'] = $padV;

  return $padReturn;

?> 