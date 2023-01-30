<?php

  if ( strpos($padPrm [$pad] [1], 'page=') !== FALSE) {
    $padIncludeCall = padInclude ($padPrm [$pad] [1]);
    return $padIncludeCall ['data'];
  }

  $padOne = APP . "includes/$padPrm [$pad] [1]";

  return include PAD . 'pad/build/one.php';

?>