<?php

  function padSeqCheckRandom ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/random/bool.php" ) )
      return padSeqBoolRandom ( $n );

    $text = padCode ( "{sequence random, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>