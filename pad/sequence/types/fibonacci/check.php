<?php

  function padSeqCheckFibonacci ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/fibonacci/bool.php' ) )
      return padSeqBoolFibonacci ( $n, $p );

    if ( file_exists ( 'sequence/types/fibonacci/fixed.php' ) ) {
      $fixed = include 'sequence/types/fibonacci/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/fibonacci/generated.php' ) ) 
      return in_array ( $n, PADfibonacci );

    $text = padCode ( "{sequence fibonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>