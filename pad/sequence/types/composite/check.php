<?php

  function padSeqCheckComposite ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/composite/bool.php' ) )
      return padSeqBoolComposite ( $n, $p );

    if ( file_exists ( 'sequence/types/composite/fixed.php' ) ) {
      $fixed = include 'sequence/types/composite/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/composite/generated.php' ) ) 
      return in_array ( $n, PADcomposite );

    $text = padCode ( "{sequence composite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>