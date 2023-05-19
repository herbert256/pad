<?php

  $padReturn = [];

  foreach ($GLOBALS ['padCurrent'] [$padIdx] as $padK => $padV)
    $padReturn [] ['name'] = $padK;

  return $padReturn;

?>