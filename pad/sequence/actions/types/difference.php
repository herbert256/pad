<?php

  $pqTmp1 = include 'sequence/actions/types/onlyNow.php';
  $pqTmp2 = include 'sequence/actions/types/onlyStore.php';

  foreach ( $pqTmp2 as $pqAppendKey )
    $pqTmp1 [] = $pqAppendKey;

  return $pqTmp1;

?>