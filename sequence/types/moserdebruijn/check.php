<?php

  function padSeqCheckMoserdebruijn ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/moserdebruijn/bool.php' ) )
      return padSeqBoolMoserdebruijn ( $n );

    if ( file_exists ( '/pad/sequence/types/moserdebruijn/generated.php' ) ) 
      return in_array ( $n, PADmoserdebruijn );

    if ( file_exists ( '/pad/sequence/types/moserdebruijn/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/moserdebruijn/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence moserdebruijn, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>