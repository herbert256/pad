<?php

  function padSeqCheckGolomb ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/golomb/bool.php' ) )
      return padSeqBoolGolomb ( $n );

    if ( file_exists ( PAD . 'seq/types/golomb/generated.php' ) ) 
      return in_array ( $n, PADgolomb );

    if ( file_exists ( PAD . 'seq/types/golomb/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/golomb/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq golomb, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>