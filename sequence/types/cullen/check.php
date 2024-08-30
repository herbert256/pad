<?php

  function padSeqCheckCullen ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/cullen/bool.php" ) )
      return padSeqBoolCullen ( $n );

    $text = padCode ( "{sequence cullen, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>