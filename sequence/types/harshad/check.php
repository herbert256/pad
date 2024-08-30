<?php

  function padSeqCheckHarshad ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/harshad/bool.php" ) )
      return padSeqBoolHarshad ( $n );

    $text = padCode ( "{sequence harshad, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>