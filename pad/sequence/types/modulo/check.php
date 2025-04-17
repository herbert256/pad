<?php

  function pqCheckModulo ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/modulo/bool.php' ) )
      return pqBoolModulo ( $n, $p );

    if ( file_exists ( 'sequence/types/modulo/fixed.php' ) ) {
      $fixed = include 'sequence/types/modulo/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/modulo/generated.php' ) ) 
      return in_array ( $n, PADmodulo );

    $text = padCode ( "{sequence modulo='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>