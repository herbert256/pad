<?php

  function padSeqCheckFloor ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/floor/bool.php' ) )
      return padSeqBoolFloor ( $n );

    if ( file_exists ( PAD . 'seq/types/floor/generated.php' ) ) 
      return in_array ( $n, PADfloor );

    if ( file_exists ( PAD . 'seq/types/floor/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/floor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq floor, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>