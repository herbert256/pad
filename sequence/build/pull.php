<?php

  $pad_seq_pull_list = $pad_seq_store [$pad_pull_store];

  if ( $pad_seq_build == 'jump' and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 
    include PAD_HOME . "sequence/types/$pad_tag/init.php";

  foreach ( $pad_seq_pull_list as $pad_seq_loop_idx ) {
    if ( ! include 'go/one.php')
      break;

  }

?>