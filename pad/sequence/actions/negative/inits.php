<?php

  $padSeqNegativeOld = $padSeqResult;
  $padSeqResult = [];

  foreach ( $padSeqNegativeOld as $padK => $padV )
    $padSeqResult [ 'x' . $padK ] = $padV;

  $padSeqNegativeOld = $padSeqResult;

?>