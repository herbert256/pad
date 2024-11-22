<?php

  function padSeqCheckCatalan ( $f, $n ) {

    if ( file_exists ( 'seq/types/catalan/bool.php' ) )
      return padSeqBoolCatalan ( $n );

    if ( file_exists ( 'seq/types/catalan/generated.php' ) ) 
      return in_array ( $n, PADcatalan );

    if ( file_exists ( 'seq/types/catalan/fixed.php' ) ) {
      $fixed = include 'seq/types/catalan/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq catalan, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>