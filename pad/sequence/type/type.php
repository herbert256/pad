<?php

  if ( $pad_seq_pull and $pad_seq_seq <> 'pull' ) {

    $pad_seq_for = $pad_seq_store [$pad_seq_pull];

    include "for.php";

  } elseif ( $pad_seq_range and $pad_seq_seq <> 'range' ) {

    $pad_seq_for = pad_get_range ( $pad_seq_range, $pad_seq_inc );

    include "for.php";

  } else

    include "$pad_seq_build.php";

?>