<?php

  function padSeqCheckRound ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/round/bool.php" ) )
      return padSeqBoolRound ( $n );

    $text = padCode ( "{sequence round, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>