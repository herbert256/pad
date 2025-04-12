<?php

  function padSeqBoolMultiple ( $n, $p=0 ) {

    global $padSeqParm;

    return ( $n == ceil ( $n / $padSeqParm) * $padSeqParm );

  }

?>