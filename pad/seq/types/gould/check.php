<?php

  function padSeqCheckGould ( $f, $n ) {

    if ( file_exists ( PAD . 'seq/types/gould/bool.php' ) )
      return padSeqBoolGould ( $n );

    if ( file_exists ( PAD . 'seq/types/gould/generated.php' ) ) 
      return in_array ( $n, PADgould );

    if ( file_exists ( PAD . 'seq/types/gould/fixed.php' ) ) {
      $fixed = include PAD . 'seq/types/gould/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq gould, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>