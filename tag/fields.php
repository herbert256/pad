<?php

  $pad_return = [];

  foreach ($GLOBALS['pad_current'] [$pad_idx] as $pad_k => $pad_v)
    $pad_return [] = $pad_k;

  return $pad_return;

?>