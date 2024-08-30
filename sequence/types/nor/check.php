<?php

  function padSeqCheckNor ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/nor/bool.php" ) )
      return padSeqBoolNor ( $n );

    $text = padCode ( "{sequence nor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>