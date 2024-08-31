<?php

  function padSeqCheckLucky ( $f, $n ) {

    if ( file_exists ( '/pad/sequence/types/lucky/bool.php' ) )
      return padSeqBoolLucky ( $n );

    if ( file_exists ( '/pad/sequence/types/lucky/generated.php' ) ) 
      return in_array ( $n, PADlucky );

    if ( file_exists ( '/pad/sequence/types/lucky/fixed.php' ) ) {
      $fixed = include '/pad/sequence/types/lucky/fixed.php';
      return in_array ( $n, $fixed );

    }

    $text = padCode ( "{sequence lucky, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>