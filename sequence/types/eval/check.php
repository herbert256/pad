<?php

  function padSeqCheckEval ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/eval/bool.php" ) )
      return padSeqBoolEval ( $n );

    $text = padCode ( "{sequence eval, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>