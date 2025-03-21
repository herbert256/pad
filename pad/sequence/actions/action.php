<?php

  $padSeqActionList = padExplode ( $padPrmValue, '|' );
  $padSeqActionName = array_shift ( $padSeqActionList );

  include 'sequence/actions/go.php';

?>