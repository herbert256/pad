<?php

  function padSeqCheckSylvester ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/sylvester/bool.php" ) )
      return padSeqBoolSylvester ( $n );

    $text = padCode ( "{sequence sylvester, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>