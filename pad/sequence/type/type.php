<?php

  if ( $padSeq_pull and $padSeq_seq <> 'pull' ) {

    $padSeq_for = $padSequenceStore [$padSeq_pull];

    include "for.php";

  } elseif ( $padSeq_range and $padSeq_seq <> 'range' ) {

    $padSeq_for = padGetRange ( $padSeq_range, $padSeq_inc );

    include "for.php";

  } else

    include "$padSeq_build.php";

?>