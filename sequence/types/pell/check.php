<?php

  function padSeqCheckPell ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/pell/bool.php' ) )
      return padSeqBoolPell ( $n );

    if ( file_exists ( '/pad/sequence/types/pell/generated.php' ) ) 
      return in_array ( $n, PADpell );

    if ( file_exists ( '/pad/sequence/types/pell/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/pell/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence pell, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>