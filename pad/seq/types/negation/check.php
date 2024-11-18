<?php

  function padSeqCheckNegation ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/negation/bool.php' ) )
      return padSeqBoolNegation ( $n );

    if ( file_exists ( PAD . 'seq/types/negation/generated.php' ) ) 
      return in_array ( $n, PADnegation );

    if ( file_exists ( PAD . 'seq/types/negation/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/negation/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq negation, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>