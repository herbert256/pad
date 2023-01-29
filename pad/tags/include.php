<?php

  if ( strpos($padPrm [$pad] [0], 'page=') !== FALSE) {
    $padIncludeCall = padInclude ($padPrm [$pad] [0]);
    return $padIncludeCall ['data'];
  }

  $padOne = APP . "includes/$padPrm [$pad] [0]";

  return include PAD . 'pad/build/one.php';

?>