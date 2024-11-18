<?php

  function padSeqCheckStrong ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/strong/bool.php' ) )
      return padSeqBoolStrong ( $n );

    if ( file_exists ( PAD . 'seq/types/strong/generated.php' ) ) 
      return in_array ( $n, PADstrong );

    if ( file_exists ( PAD . 'seq/types/strong/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/strong/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq strong, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>