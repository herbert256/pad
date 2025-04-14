<?php

  function padSeqCheckBiquadratic ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/biquadratic/bool.php' ) )
      return padSeqBoolBiquadratic ( $n, $p );

    if ( file_exists ( 'sequence/types/biquadratic/fixed.php' ) ) {
      $fixed = include 'sequence/types/biquadratic/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/biquadratic/generated.php' ) ) 
      return in_array ( $n, PADbiquadratic );

    $text = padCode ( "{sequence biquadratic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>