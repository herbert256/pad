<?php

  function padSeqCheckGolomb ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/golomb/bool.php" ) )
      return padSeqBoolGolomb ( $n );

    $text = padCode ( "{sequence golomb, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>