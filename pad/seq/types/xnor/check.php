<?php

  function padSeqCheckXnor ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/xnor/bool.php' ) )
      return padSeqBoolXnor ( $n );

    if ( file_exists ( PAD . 'seq/types/xnor/generated.php' ) ) 
      return in_array ( $n, PADxnor );

    if ( file_exists ( PAD . 'seq/types/xnor/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/xnor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq xnor, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>