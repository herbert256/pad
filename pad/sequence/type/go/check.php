<?php

  foreach ( $pad_seq_checks as $pad_seq_check )
    if ( ! include PAD . "sequence/checks/$pad_seq_check.php" )     
      return false;

  return true;

?>