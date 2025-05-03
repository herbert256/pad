<?php

  function pqCheckKolakoski ( $f, $n, $p ) {

    if ( file_exists ( 'sequence/types/kolakoski/bool.php' ) )
      return pqBoolKolakoski ( $n, $p );

    if ( file_exists ( 'sequence/types/kolakoski/fixed.php' ) ) {
      $pqParm = $p;
      $fixed = include 'sequence/types/kolakoski/fixed.php';
      return in_array ( $n, $fixed );
    }

    if ( file_exists ( 'sequence/types/kolakoski/generated.php' ) ) 
      return in_array ( $n, PADkolakoski );

    #$text = padCode ( "{sequence kolakoski, from=$n, to=$n}{\$sequence},{/sequence}" );
    $text = padCode ( "{sequence kolakoski, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>