<?php

  function padSeqCheckGould ( $f, $n ) {

    if ( file_exists ( 'seq/types/gould/bool.php' ) )
      return padSeqBoolGould ( $n );

    if ( file_exists ( 'seq/types/gould/generated.php' ) ) 
      return in_array ( $n, PADgould );

    if ( file_exists ( 'seq/types/gould/fixed.php' ) ) {
      $fixed = include 'seq/types/gould/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence gould, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>