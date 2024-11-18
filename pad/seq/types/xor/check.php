<?php

  function padSeqCheckXor ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/xor/bool.php' ) )
      return padSeqBoolXor ( $n );

    if ( file_exists ( PAD . 'seq/types/xor/generated.php' ) ) 
      return in_array ( $n, PADxor );

    if ( file_exists ( PAD . 'seq/types/xor/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/xor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq xor, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>