<?php

  function padSeqCheckFibonacci ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/fibonacci/bool.php' ) )
      return padSeqBoolFibonacci ( $n );

    if ( file_exists ( PAD . 'seq/types/fibonacci/generated.php' ) ) 
      return in_array ( $n, PADfibonacci );

    if ( file_exists ( PAD . 'seq/types/fibonacci/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/fibonacci/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq fibonacci, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>