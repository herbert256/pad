<?php

  $padSeqActionList = padExplode ( $padPrmValue, '|' );
  $padSeqActionName = array_shift ( $padSeqActionList );

  include 'seq/actions/go.php';

?>