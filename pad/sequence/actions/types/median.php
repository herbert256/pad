<?php

  $pqTmp    = count ( $pqResult ) / 2;
  $pqMedian = (int) $pqTmp;

  return [ 1 => $pqResult [$pqMedian] ];

?>