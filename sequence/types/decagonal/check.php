<?php

  function padSeqCheckDecagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/decagonal/bool.php" ) )
      return padSeqBoolDecagonal ( $n );

    $text = padCode ( "{sequence decagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>