<?php

  function padSeqCheckPolite ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/polite/bool.php' ) )
      return padSeqBoolPolite ( $n );

    if ( file_exists ( '/pad/sequence/types/polite/generated.php' ) ) 
      return in_array ( $n, PADpolite );

    if ( file_exists ( '/pad/sequence/types/polite/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/polite/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence polite, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>