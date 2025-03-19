<?php

  function padSeqCheckCullen ( $f, $n ) {

    if ( file_exists ( 'seq/types/cullen/bool.php' ) )
      return padSeqBoolCullen ( $n );

    if ( file_exists ( 'seq/types/cullen/generated.php' ) ) 
      return in_array ( $n, PADcullen );

    if ( file_exists ( 'seq/types/cullen/fixed.php' ) ) {
      $fixed = include 'seq/types/cullen/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence cullen, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>