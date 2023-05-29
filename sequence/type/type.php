<?php

  if ( $padSeqPull and $padSeqSeq <> 'pull' ) {

    $padSeqFor = $padSeqStore [$padSeqPull];

    include pad . "sequence/type/for.php";

  } elseif ( $padSeqRange and $padSeqSeq <> 'range' ) {

    $padSeqFor = padGetRange ( $padSeqRange, $padSeqInc );

    include pad . "sequence/type/for.php";

  } else

    include pad . "sequence/type/$padSeqBuild.php";

?>