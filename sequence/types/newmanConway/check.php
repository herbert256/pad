<?php

  function padSeqCheckNewmanConway ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/newmanConway/bool.php' ) )
      return padSeqBoolNewmanConway ( $n );

    if ( file_exists ( '/pad/sequence/types/newmanConway/generated.php' ) ) 
      return in_array ( $n, PADnewmanConway );

    if ( file_exists ( '/pad/sequence/types/newmanConway/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/newmanConway/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence newmanConway, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>