<?php

  function padSeqCheckHarshad ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/harshad/bool.php' ) )
      return padSeqBoolHarshad ( $n );

    if ( file_exists ( PAD . 'seq/types/harshad/generated.php' ) ) 
      return in_array ( $n, PADharshad );

    if ( file_exists ( PAD . 'seq/types/harshad/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/harshad/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq harshad, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>