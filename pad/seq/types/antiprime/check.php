<?php

  function padSeqCheckAntiprime ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/antiprime/bool.php' ) )
      return padSeqBoolAntiprime ( $n );

    if ( file_exists ( PAD . 'seq/types/antiprime/generated.php' ) ) 
      return in_array ( $n, PADantiprime );

    if ( file_exists ( PAD . 'seq/types/antiprime/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/antiprime/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq antiprime, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>