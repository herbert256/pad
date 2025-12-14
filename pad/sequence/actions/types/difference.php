<?php

  $pqTmp = $pqResult;
  include PQ . 'actions/types/onlyNow.php';
  $pqTmp1 = $pqResult;
  $pqResult = $pqTmp;

  include PQ . 'actions/types/onlyStore.php';
  $pqTmp2 = $pqResult;

  foreach ( $pqTmp2 as $pqAppendKey )
    $pqTmp1 [] = $pqAppendKey;

  $pqResult = $pqTmp1;

?>