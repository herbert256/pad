<?php

  function padSeqCheckKynea ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/kynea/bool.php' ) )
      return padSeqBoolKynea ( $n, $p );

    if ( file_exists ( 'sequence/types/kynea/fixed.php' ) ) {
      $fixed = include 'sequence/types/kynea/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/kynea/generated.php' ) ) 
      return in_array ( $n, PADkynea );

    $text = padCode ( "{sequence kynea, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>