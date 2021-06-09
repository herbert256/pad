<?php

  $pad_checks = [];

  foreach ( $pad_parms_tag as $pad_k => $pad_v )
    if ( $pad_k <> $pad_tag and pad_file_exists ( PAD_HOME . "sequence/types/$pad_k/check.php" ) )
      $pad_checks [] = $pad_k;

?>