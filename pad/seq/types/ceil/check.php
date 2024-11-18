<?php

  function padSeqCheckCeil ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/ceil/bool.php' ) )
      return padSeqBoolCeil ( $n );

    if ( file_exists ( PAD . 'seq/types/ceil/generated.php' ) ) 
      return in_array ( $n, PADceil );

    if ( file_exists ( PAD . 'seq/types/ceil/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/ceil/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq ceil, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>