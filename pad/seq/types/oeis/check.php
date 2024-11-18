<?php

  function padSeqCheckOeis ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/oeis/bool.php' ) )
      return padSeqBoolOeis ( $n );

    if ( file_exists ( PAD . 'seq/types/oeis/generated.php' ) ) 
      return in_array ( $n, PADoeis );

    if ( file_exists ( PAD . 'seq/types/oeis/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/oeis/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq oeis, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>