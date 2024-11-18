<?php

  function padSeqCheckMoserdebruijn ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/moserdebruijn/bool.php' ) )
      return padSeqBoolMoserdebruijn ( $n );

    if ( file_exists ( PAD . 'seq/types/moserdebruijn/generated.php' ) ) 
      return in_array ( $n, PADmoserdebruijn );

    if ( file_exists ( PAD . 'seq/types/moserdebruijn/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/moserdebruijn/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq moserdebruijn, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>