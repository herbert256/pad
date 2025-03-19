<?php

  function padSeqCheckBiquadratic ( $f, $n ) {

    if ( file_exists ( 'seq/types/biquadratic/bool.php' ) )
      return padSeqBoolBiquadratic ( $n );

    if ( file_exists ( 'seq/types/biquadratic/generated.php' ) ) 
      return in_array ( $n, PADbiquadratic );

    if ( file_exists ( 'seq/types/biquadratic/fixed.php' ) ) {
      $fixed = include 'seq/types/biquadratic/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence biquadratic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>