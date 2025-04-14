<?php

  function padSeqCheckOctahedral ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/octahedral/bool.php' ) )
      return padSeqBoolOctahedral ( $n, $p );

    if ( file_exists ( 'sequence/types/octahedral/fixed.php' ) ) {
      $fixed = include 'sequence/types/octahedral/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/octahedral/generated.php' ) ) 
      return in_array ( $n, PADoctahedral );

    $text = padCode ( "{sequence octahedral, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>