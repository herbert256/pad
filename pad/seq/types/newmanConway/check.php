<?php

  function padSeqCheckNewmanConway ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/newmanConway/bool.php' ) )
      return padSeqBoolNewmanConway ( $n );

    if ( file_exists ( PAD . 'seq/types/newmanConway/generated.php' ) ) 
      return in_array ( $n, PADnewmanConway );

    if ( file_exists ( PAD . 'seq/types/newmanConway/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/newmanConway/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq newmanConway, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>