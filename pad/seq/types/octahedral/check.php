<?php

  function padSeqCheckOctahedral ( $f, $n ) {

    if ( file_exists ( 'seq/types/octahedral/bool.php' ) )
      return padSeqBoolOctahedral ( $n );

    if ( file_exists ( 'seq/types/octahedral/generated.php' ) ) 
      return in_array ( $n, PADoctahedral );

    if ( file_exists ( 'seq/types/octahedral/fixed.php' ) ) {
      $fixed = include 'seq/types/octahedral/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{seq octahedral, from=$f, stop=$n, try=$n}{\$seq},{/seq}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>