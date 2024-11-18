<?php

  function padSeqCheckSubstract ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/substract/bool.php' ) )
      return padSeqBoolSubstract ( $n );

    if ( file_exists ( PAD . 'seq/types/substract/generated.php' ) ) 
      return in_array ( $n, PADsubstract );

    if ( file_exists ( PAD . 'seq/types/substract/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/substract/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq substract, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>