<?php

  $pReturn = [];

  foreach ($GLOBALS['pCurrent'] [$pIdx] as $pK => $pV)
    $pReturn [] ['name'] = $pK;

  return $pReturn;

?>