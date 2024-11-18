<?php

  function padSeqCheckGet ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/get/bool.php' ) )
      return padSeqBoolGet ( $n );

    if ( file_exists ( PAD . 'seq/types/get/generated.php' ) ) 
      return in_array ( $n, PADget );

    if ( file_exists ( PAD . 'seq/types/get/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/get/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq get, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>