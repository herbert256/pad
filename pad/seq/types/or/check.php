<?php

  function padSeqCheckOr ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/or/bool.php' ) )
      return padSeqBoolOr ( $n );

    if ( file_exists ( PAD . 'seq/types/or/generated.php' ) ) 
      return in_array ( $n, PADor );

    if ( file_exists ( PAD . 'seq/types/or/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/or/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq or, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>