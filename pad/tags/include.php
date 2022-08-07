<?php

  if ( strpos($pPrm [$p], 'page=') !== FALSE) {
    $pInclude_call = pInclude ($pPrm [$p]);
    return $pInclude_call ['data'];
  }

  $pOne = APP . "includes/$pPrm [$p]";

  return include PAD . 'build/one.php';

?>