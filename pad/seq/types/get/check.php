<?php

  function padSeqCheckGet ( $f, $n ) {

    if ( file_exists ( 'seq/types/get/bool.php' ) )
      return padSeqBoolGet ( $n );

    if ( file_exists ( 'seq/types/get/generated.php' ) ) 
      return in_array ( $n, PADget );

    if ( file_exists ( 'seq/types/get/fixed.php' ) ) {
      $fixed = include 'seq/types/get/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence get, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>