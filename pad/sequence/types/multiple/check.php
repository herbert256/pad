<?php

  function padSeqCheckMultiple ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/multiple/bool.php' ) )
      return padSeqBoolMultiple ( $n, $p );

    if ( file_exists ( 'sequence/types/multiple/fixed.php' ) ) {
      $fixed = include 'sequence/types/multiple/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/multiple/generated.php' ) ) 
      return in_array ( $n, PADmultiple );

    $text = padCode ( "{sequence multiple='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>