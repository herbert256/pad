<?php

  function pqCheckPower ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/power/bool.php' ) )
      return pqBoolPower ( $n, $p );

    if ( file_exists ( 'sequence/types/power/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/power/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/power/generated.php' ) ) 
      return in_array ( $n, PADpower );

    #$text = padCode ( "{sequence power='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence power='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>