<?php

  function padSeqCheckBiquadratic ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/biquadratic/bool.php' ) )
      return padSeqBoolBiquadratic ( $n );

    if ( file_exists ( '/pad/sequence/types/biquadratic/generated.php' ) ) 
      return in_array ( $n, PADbiquadratic );

    if ( file_exists ( '/pad/sequence/types/biquadratic/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/biquadratic/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence biquadratic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>