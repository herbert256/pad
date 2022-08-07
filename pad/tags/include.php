<?php

  if ( strpos($pParm, 'page=') !== FALSE) {
    $pInclude_call = pInclude ($pParm);
    return $pInclude_call ['data'];
  }

  $pOne = APP . "includes/$pParm";

  return include PAD . 'build/one.php';

?>