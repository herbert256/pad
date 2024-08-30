<?php

  function padSeqCheckOctagonal ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/octagonal/bool.php" ) )
      return padSeqBoolOctagonal ( $n );

    $text = padCode ( "{sequence octagonal, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>