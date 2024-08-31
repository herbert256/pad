<?php

  function padSeqCheckStrong ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/strong/bool.php' ) )
      return padSeqBoolStrong ( $n );

    if ( file_exists ( '/pad/sequence/types/strong/generated.php' ) ) 
      return in_array ( $n, PADstrong );

    if ( file_exists ( '/pad/sequence/types/strong/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/strong/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence strong, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>