<?php

  if  ( $pad_seq_seq <> 'loop' and pad_file_exists (PAD . "sequence/types/$pad_seq_seq/$pad_seq_seq.php") )  {

    return include PAD . "sequence/types/$pad_seq_seq/$pad_seq_seq.php";

  }

  if ( $pad_seq_pull ) {

    $pad_seq_for = $pad_seq_store [$pad_seq_pull];

    return include "for.php";

  }

  if ( $pad_seq_range and $pad_seq_seq <> 'range' ) {

    $pad_seq_for = pad_get_range ( $pad_seq_range, $pad_seq_inc );

    return include "for.php";

  }

  if ( count($pad_seq_for) ) 
    return include "for.php";

  return include "$pad_seq_build.php";

?>