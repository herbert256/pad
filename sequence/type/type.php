<?php

  if ( $padSeqPull and $padSeqSeq <> 'pull' ) {

    $padSeqFor = $padSeqStore [$padSeqPull];

    include "for.php";

  } elseif ( $padSeqRange and $padSeqSeq <> 'range' ) {

    $padSeqFor = padGetRange ( $padSeqRange, $padSeqInc );

    include "for.php";

  } else

    include "$padSeqBuild.php";

?>