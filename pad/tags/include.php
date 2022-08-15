<?php

  if ( strpos($padPrm [$pad], 'page=') !== FALSE) {
    $padIncludeCall = padInclude ($padPrm [$pad]);
    return $padIncludeCall ['data'];
  }

  $padOne = APP . "includes/$padPrm[$pad]";

  return include PAD . 'build/one.php';

?>