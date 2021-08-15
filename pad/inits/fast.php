<?php
  
  $pad_fast = pad_db ( "field vars from links where link = '{1}'", [ 1 => $_SERVER['QUERY_STRING'] ] );

  if ( ! $pad_fast )
    pad_boot_error ("Shortcut to stored parameters not found: |" . $_SERVER['QUERY_STRING'] . "|");
  
  extract ( unserialize ( $pad_fast ), EXTR_OVERWRITE );

  include PAD_HOME . 'pad/config/config.php';

?>