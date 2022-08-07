<?php

  $pReturn = [];

  foreach ($GLOBALS['pCurrent'] [$pIdx] as $pK => $pad_v)
    $pReturn [] ['name'] = $pK;

  return $pReturn;

?>