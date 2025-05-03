<?php

  function pqCheckChance ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/chance/bool.php' ) )
      return pqBoolChance ( $n, $p );

    if ( file_exists ( 'sequence/types/chance/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/chance/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/chance/generated.php' ) ) 
      return in_array ( $n, PADchance );

    #$text = padCode ( "{sequence chance='$p', from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence chance='$p', from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>