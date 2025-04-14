<?php

  function padSeqCheckPentagonal ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/pentagonal/bool.php' ) )
      return padSeqBoolPentagonal ( $n, $p );

    if ( file_exists ( 'sequence/types/pentagonal/fixed.php' ) ) {
      $fixed = include 'sequence/types/pentagonal/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/pentagonal/generated.php' ) ) 
      return in_array ( $n, PADpentagonal );

    $text = padCode ( "{sequence pentagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>