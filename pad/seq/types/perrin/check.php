<?php

  function padSeqCheckPerrin ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/perrin/bool.php' ) )
      return padSeqBoolPerrin ( $n );

    if ( file_exists ( PAD . 'seq/types/perrin/generated.php' ) ) 
      return in_array ( $n, PADperrin );

    if ( file_exists ( PAD . 'seq/types/perrin/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/perrin/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq perrin, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>