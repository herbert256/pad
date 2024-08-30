<?php

  function padSeqCheckHexagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/hexagonal/bool.php" ) )
      return padSeqBoolHexagonal ( $n );

    $text = padCode ( "{sequence hexagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>