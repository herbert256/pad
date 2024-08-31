<?php

  function padSeqCheckCullen ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/cullen/bool.php' ) )
      return padSeqBoolCullen ( $n );

    if ( file_exists ( '/pad/sequence/types/cullen/generated.php' ) ) 
      return in_array ( $n, PADcullen );

    if ( file_exists ( '/pad/sequence/types/cullen/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/cullen/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence cullen, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>