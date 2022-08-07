<?php

  $pReturn = [];
  $pI      = 0;

  foreach ($GLOBALS['pCurrent'] [$pIdx] as $pK => $pV) {
    $pI++;
    $pReturn [$pI] ['name']  = $pK;
    $pReturn [$pI] ['value'] = $pV;
  }

  return $pReturn;

?> 