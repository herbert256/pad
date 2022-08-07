<?php
    
  if ( isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] and strpos($_SERVER['QUERY_STRING'], '=') === FALSE ) {

    $pFast = pDb ( "field vars from links where link = '{1}'", [ 1 => $_SERVER['QUERY_STRING'] ] );

    if ( ! $pFast )
      pBoot_error ("Shortcut to stored parameters not found: |" . $_SERVER['QUERY_STRING'] . "|");
    
    extract ( unserialize ( $pFast ), EXTR_OVERWRITE );

    include PAD . 'config/config.php';

  }
  
?>