<?php

  $pReturn = [];
  $pI      = 0;

  foreach ($GLOBALS['pCurrent'] [$pIdx] as $pK => $pad_v) {
    $pI++;
    $pReturn [$pI] ['name']  = $pK;
    $pReturn [$pI] ['value'] = $pad_v;
  }

  return $pReturn;

?> 