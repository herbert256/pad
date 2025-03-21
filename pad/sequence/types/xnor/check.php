<?php

  function padSeqCheckXnor ( $f, $n ) {

    if ( file_exists ( 'sequence/types/xnor/bool.php' ) )
      return padSeqBoolXnor ( $n );

    if ( file_exists ( 'sequence/types/xnor/generated.php' ) ) 
      return in_array ( $n, PADxnor );

    if ( file_exists ( 'sequence/types/xnor/fixed.php' ) ) {
      $fixed = include 'sequence/types/xnor/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence xnor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>