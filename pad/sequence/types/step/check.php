<?php

  function padSeqCheckStep ( $f, $n ) {

    if ( file_exists ( 'sequence/types/step/bool.php' ) )
      return padSeqBoolStep ( $n );

    if ( file_exists ( 'sequence/types/step/generated.php' ) ) 
      return in_array ( $n, PADstep );

    if ( file_exists ( 'sequence/types/step/fixed.php' ) ) {
      $fixed = include 'sequence/types/step/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence step, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>