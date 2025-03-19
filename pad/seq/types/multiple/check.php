<?php

  function padSeqCheckMultiple ( $f, $n ) {

    if ( file_exists ( 'seq/types/multiple/bool.php' ) )
      return padSeqBoolMultiple ( $n );

    if ( file_exists ( 'seq/types/multiple/generated.php' ) ) 
      return in_array ( $n, PADmultiple );

    if ( file_exists ( 'seq/types/multiple/fixed.php' ) ) {
      $fixed = include 'seq/types/multiple/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence multiple, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>