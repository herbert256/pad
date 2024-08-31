<?php

  function padSeqCheckFibonacci ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/fibonacci/bool.php' ) )
      return padSeqBoolFibonacci ( $n );

    if ( file_exists ( '/pad/sequence/types/fibonacci/generated.php' ) ) 
      return in_array ( $n, PADfibonacci );

    if ( file_exists ( '/pad/sequence/types/fibonacci/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/fibonacci/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence fibonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>