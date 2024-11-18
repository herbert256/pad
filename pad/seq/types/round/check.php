<?php

  function padSeqCheckRound ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/round/bool.php' ) )
      return padSeqBoolRound ( $n );

    if ( file_exists ( PAD . 'seq/types/round/generated.php' ) ) 
      return in_array ( $n, PADround );

    if ( file_exists ( PAD . 'seq/types/round/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/round/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq round, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>