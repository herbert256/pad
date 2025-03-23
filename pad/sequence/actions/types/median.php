<?php

  $padSeqTmp    = count ( $padSeqResult ) / 2;
  $padSeqMedian = (int) $padSeqTmp;

  return [ 1 => $padSeqResult [$padSeqMedian] ];

?>