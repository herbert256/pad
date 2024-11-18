<?php

  function padSeqCheckOdd ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/odd/bool.php' ) )
      return padSeqBoolOdd ( $n );

    if ( file_exists ( PAD . 'seq/types/odd/generated.php' ) ) 
      return in_array ( $n, PADodd );

    if ( file_exists ( PAD . 'seq/types/odd/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/odd/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq odd, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>