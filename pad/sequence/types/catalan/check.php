<?php

  function pqCheckCatalan ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/catalan/bool.php' ) )
      return pqBoolCatalan ( $n, $p );

    if ( file_exists ( 'sequence/types/catalan/fixed.php' ) ) {
      $fixed = include 'sequence/types/catalan/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/catalan/generated.php' ) ) 
      return in_array ( $n, PADcatalan );

    $text = padCode ( "{sequence catalan, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>