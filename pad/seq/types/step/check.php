<?php

  function padSeqCheckStep ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/step/bool.php' ) )
      return padSeqBoolStep ( $n );

    if ( file_exists ( PAD . 'seq/types/step/generated.php' ) ) 
      return in_array ( $n, PADstep );

    if ( file_exists ( PAD . 'seq/types/step/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/step/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq step, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>