<?php

  function padSeqCheckOctagonal ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/octagonal/bool.php' ) )
      return padSeqBoolOctagonal ( $n );

    if ( file_exists ( PAD . 'seq/types/octagonal/generated.php' ) ) 
      return in_array ( $n, PADoctagonal );

    if ( file_exists ( PAD . 'seq/types/octagonal/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/octagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq octagonal, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>