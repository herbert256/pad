<?php

  function padSeqCheckEmirp ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/emirp/bool.php' ) )
      return padSeqBoolEmirp ( $n );

    if ( file_exists ( PAD . 'seq/types/emirp/generated.php' ) ) 
      return in_array ( $n, PADemirp );

    if ( file_exists ( PAD . 'seq/types/emirp/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/emirp/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq emirp, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>