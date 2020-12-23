<?php

  $pad_return = [];

  foreach ($GLOBALS['pad_current'] [$pad_idx] as $pad_k => $pad_v)
    $pad_return [] ['value'] = $pad_v;

  return $pad_return;

?> 