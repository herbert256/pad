<?php

  if ( ! isset ( $padPageSet ) and isset ( $_SERVER['QUERY_STRING'] ) and    
       $_SERVER['QUERY_STRING'] and strpos ( $_SERVER['QUERY_STRING'], '=' ) === FALSE ) {

    $padFast = padDb ( "field vars from links where link = '{1}'", [ 1 => $_SERVER['QUERY_STRING'] ] );

    if ( ! $padFast )
      padBootError ("Shortcut to stored parameters not found: |" . $_SERVER['QUERY_STRING'] . "|");
    
    extract ( unserialize ( $padFast ), EXTR_OVERWRITE );

    include pad . 'config/config.php';

  }

  unset ( $padPageSet );
  
?>