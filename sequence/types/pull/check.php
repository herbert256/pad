<?php

  function padSeqCheckPull ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/pull/bool.php' ) )
      return padSeqBoolPull ( $n );

    if ( file_exists ( '/pad/sequence/types/pull/generated.php' ) ) 
      return in_array ( $n, PADpull );

    if ( file_exists ( '/pad/sequence/types/pull/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/pull/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence pull, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>