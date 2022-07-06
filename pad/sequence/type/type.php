<?php

  if  ( $pad_seq_seq <> 'loop' and pad_file_exists (PAD . "sequence/types/$pad_seq_seq/$pad_seq_seq.php") ) 

    include PAD . "sequence/types/$pad_seq_seq/$pad_seq_seq.php";

  elseif ( $pad_seq_pull ) {

    $pad_seq_for = $pad_seq_store [$pad_seq_pull];

    include "for.php";

  } elseif ( $pad_seq_range and $pad_seq_seq <> 'range' ) {

    $pad_seq_for = pad_get_range ( $pad_seq_range, $pad_seq_inc );

    include "for.php";

  } else

    return include "$pad_seq_build.php";

?>