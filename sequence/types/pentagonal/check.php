<?php

  function padSeqCheckPentagonal ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/pentagonal/bool.php' ) )
      return padSeqBoolPentagonal ( $n );

    if ( file_exists ( '/pad/sequence/types/pentagonal/generated.php' ) ) 
      return in_array ( $n, PADpentagonal );

    if ( file_exists ( '/pad/sequence/types/pentagonal/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/pentagonal/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence pentagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>