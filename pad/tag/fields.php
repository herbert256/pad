<?php

  $pad_return = [];
  $pad_i      = 0;

  foreach ($GLOBALS['pad_current'] [$pad_idx] as $pad_k => $pad_v) {
    $pad_i++;
    $pad_return [$pad_i] ['name']  = $pad_k;
    $pad_return [$pad_i] ['value'] = $pad_v;
  }

  return $pad_return;

?> 