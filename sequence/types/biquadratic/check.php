<?php

  function padSeqCheckBiquadratic ( $f, $n ) {

    if ( file_exists ( "/pad/sequence/types/biquadratic/bool.php" ) )
      return padSeqBoolBiquadratic ( $n );

    $text = padCode ( "{sequence biquadratic, from=$f, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>