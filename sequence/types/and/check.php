<?php

  function padSeqCheckAnd ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/and/bool.php" ) )
      return padSeqBoolAnd ( $n );

    $text = padCode ( "{sequence and, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>