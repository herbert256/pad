<?php

  function padSeqCheckRange ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/range/bool.php' ) )
      return padSeqBoolRange ( $n );

    if ( file_exists ( '/pad/sequence/types/range/generated.php' ) ) 
      return in_array ( $n, PADrange );

    if ( file_exists ( '/pad/sequence/types/range/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/range/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence range, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>