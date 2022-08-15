<?php

  if ( strpos($padPrm [$pad], 'page=') !== FALSE) {
    $padInclude_call = padInclude ($padPrm [$pad]);
    return $padInclude_call ['data'];
  }

  $padOne = APP . "includes/$padPrm[$pad]";

  return include PAD . 'build/one.php';

?>