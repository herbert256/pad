<?php

  function padSeqCheckRepeat ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/repeat/bool.php' ) )
      return padSeqBoolRepeat ( $n );

    if ( file_exists ( PAD . 'seq/types/repeat/generated.php' ) ) 
      return in_array ( $n, PADrepeat );

    if ( file_exists ( PAD . 'seq/types/repeat/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/repeat/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq repeat, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>