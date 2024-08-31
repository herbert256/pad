<?php

  function padSeqCheckPower ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/power/bool.php' ) )
      return padSeqBoolPower ( $n );

    if ( file_exists ( '/pad/sequence/types/power/generated.php' ) ) 
      return in_array ( $n, PADpower );

    if ( file_exists ( '/pad/sequence/types/power/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/power/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence power, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>