<?php

  function padSeqBoolMultiple ( $n ) {

    global $padSeqParm;

    return ( $n == ceil ( $n / $padSeqParm) * $padSeqParm );

  }

?>