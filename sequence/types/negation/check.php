<?php

  function padSeqCheckNegation ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/negation/bool.php" ) )
      return padSeqBoolNegation ( $n );

    $text = padCode ( "{sequence negation, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>