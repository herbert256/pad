<?php

  function padSeqCheckEnneadecagonal ( $f, $n ) {

    if ( file_exists ( 'seq/types/enneadecagonal/bool.php' ) )
      return padSeqBoolEnneadecagonal ( $n );

    if ( file_exists ( 'seq/types/enneadecagonal/generated.php' ) ) 
      return in_array ( $n, PADenneadecagonal );

    if ( file_exists ( 'seq/types/enneadecagonal/fixed.php' ) ) {
      $fixed = include 'seq/types/enneadecagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq enneadecagonal, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>