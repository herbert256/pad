<?php

  function padSeqCheckKynea ( $f, $n ) {

    if ( file_exists ( 'seq/types/kynea/bool.php' ) )
      return padSeqBoolKynea ( $n );

    if ( file_exists ( 'seq/types/kynea/generated.php' ) ) 
      return in_array ( $n, PADkynea );

    if ( file_exists ( 'seq/types/kynea/fixed.php' ) ) {
      $fixed = include 'seq/types/kynea/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence kynea, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>