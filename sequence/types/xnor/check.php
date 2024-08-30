<?php

  function padSeqCheckXnor ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/xnor/bool.php" ) )
      return padSeqBoolXnor ( $n );

    $text = padCode ( "{sequence xnor, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>