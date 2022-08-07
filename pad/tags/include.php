<?php

  if ( strpos($pParm [$p], 'page=') !== FALSE) {
    $pInclude_call = pInclude ($pParm [$p]);
    return $pInclude_call ['data'];
  }

  $pOne = APP . "includes/$pParm [$p]";

  return include PAD . 'build/one.php';

?>