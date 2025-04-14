<?php

  function padSeqCheckPell ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/pell/bool.php' ) )
      return padSeqBoolPell ( $n, $p );

    if ( file_exists ( 'sequence/types/pell/fixed.php' ) ) {
      $fixed = include 'sequence/types/pell/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/pell/generated.php' ) ) 
      return in_array ( $n, PADpell );

    $text = padCode ( "{sequence pell, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>