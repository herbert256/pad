<?php

  if ( $pSeq_pull and $pSeq_seq <> 'pull' ) {

    $pSeq_for = $pSequence_store [$pSeq_pull];

    include "for.php";

  } elseif ( $pSeq_range and $pSeq_seq <> 'range' ) {

    $pSeq_for = pGet_range ( $pSeq_range, $pSeq_inc );

    include "for.php";

  } else

    include "$pSeq_build.php";

?>