<?php

  function padSeqCheckEnneadecagonal ( $f, $n ) {

    if ( file_exists ( 'sequence/types/enneadecagonal/bool.php' ) )
      return padSeqBoolEnneadecagonal ( $n );

    if ( file_exists ( 'sequence/types/enneadecagonal/generated.php' ) ) 
      return in_array ( $n, PADenneadecagonal );

    if ( file_exists ( 'sequence/types/enneadecagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/enneadecagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence enneadecagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>