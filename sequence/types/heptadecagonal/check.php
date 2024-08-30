<?php

  function padSeqCheckHeptadecagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/heptadecagonal/bool.php" ) )
      return padSeqBoolHeptadecagonal ( $n );

    $text = padCode ( "{sequence heptadecagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>