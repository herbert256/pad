<?php

  function padSeqCheckComposite ( $f, $n ) {

    if ( file_exists ( 'seq/types/composite/bool.php' ) )
      return padSeqBoolComposite ( $n );

    if ( file_exists ( 'seq/types/composite/generated.php' ) ) 
      return in_array ( $n, PADcomposite );

    if ( file_exists ( 'seq/types/composite/fixed.php' ) ) {
      $fixed = include 'seq/types/composite/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence composite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>