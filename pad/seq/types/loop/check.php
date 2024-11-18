<?php

  function padSeqCheckLoop ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/loop/bool.php' ) )
      return padSeqBoolLoop ( $n );

    if ( file_exists ( PAD . 'seq/types/loop/generated.php' ) ) 
      return in_array ( $n, PADloop );

    if ( file_exists ( PAD . 'seq/types/loop/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/loop/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq loop, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>