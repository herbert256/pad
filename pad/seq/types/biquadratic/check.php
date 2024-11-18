<?php

  function padSeqCheckBiquadratic ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/biquadratic/bool.php' ) )
      return padSeqBoolBiquadratic ( $n );

    if ( file_exists ( PAD . 'seq/types/biquadratic/generated.php' ) ) 
      return in_array ( $n, PADbiquadratic );

    if ( file_exists ( PAD . 'seq/types/biquadratic/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/biquadratic/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq biquadratic, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>