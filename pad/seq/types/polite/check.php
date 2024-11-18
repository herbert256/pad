<?php

  function padSeqCheckPolite ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/polite/bool.php' ) )
      return padSeqBoolPolite ( $n );

    if ( file_exists ( PAD . 'seq/types/polite/generated.php' ) ) 
      return in_array ( $n, PADpolite );

    if ( file_exists ( PAD . 'seq/types/polite/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/polite/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq polite, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>