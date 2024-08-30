<?php

  function padSeqCheckTribonacci ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/tribonacci/bool.php" ) )
      return padSeqBoolTribonacci ( $n );

    $text = padCode ( "{sequence tribonacci, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>