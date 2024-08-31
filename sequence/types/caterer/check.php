<?php

  function padSeqCheckCaterer ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/caterer/bool.php' ) )
      return padSeqBoolCaterer ( $n );

    if ( file_exists ( '/pad/sequence/types/caterer/generated.php' ) ) 
      return in_array ( $n, PADcaterer );

    if ( file_exists ( '/pad/sequence/types/caterer/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/caterer/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence caterer, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>