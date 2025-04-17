<?php

  function pqCheckLucas ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/lucas/bool.php' ) )
      return pqBoolLucas ( $n, $p );

    if ( file_exists ( 'sequence/types/lucas/fixed.php' ) ) {
      $fixed = include 'sequence/types/lucas/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/lucas/generated.php' ) ) 
      return in_array ( $n, PADlucas );

    $text = padCode ( "{sequence lucas, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>