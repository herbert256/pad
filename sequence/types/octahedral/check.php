<?php

  function padSeqCheckOctahedral ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/octahedral/bool.php' ) )
      return padSeqBoolOctahedral ( $n );

    if ( file_exists ( '/pad/sequence/types/octahedral/generated.php' ) ) 
      return in_array ( $n, PADoctahedral );

    if ( file_exists ( '/pad/sequence/types/octahedral/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/octahedral/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence octahedral, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>