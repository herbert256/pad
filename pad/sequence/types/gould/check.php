<?php

  function padSeqCheckGould ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/gould/bool.php' ) )
      return padSeqBoolGould ( $n, $p );

    if ( file_exists ( 'sequence/types/gould/fixed.php' ) ) {
      $fixed = include 'sequence/types/gould/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/gould/generated.php' ) ) 
      return in_array ( $n, PADgould );

    $text = padCode ( "{sequence gould, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>