<?php

  function padSeqCheckDecagonal ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/decagonal/bool.php' ) )
      return padSeqBoolDecagonal ( $n );

    if ( file_exists ( PAD . 'seq/types/decagonal/generated.php' ) ) 
      return in_array ( $n, PADdecagonal );

    if ( file_exists ( PAD . 'seq/types/decagonal/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/decagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq decagonal, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>