<?php

  function padSeqCheckPower ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/power/bool.php' ) )
      return padSeqBoolPower ( $n, $p );

    if ( file_exists ( 'sequence/types/power/fixed.php' ) ) {
      $fixed = include 'sequence/types/power/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/power/generated.php' ) ) 
      return in_array ( $n, PADpower );

    $text = padCode ( "{sequence power='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>