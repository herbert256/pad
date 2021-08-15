<?php

  $GLOBALS ["pad_seq_$pad_seq_dir"] = [];

  foreach ( $pad_parms_tag as $pad_k => $pad_v )

    if ( $pad_k <> $pad_tag and pad_file_exists ( PAD_HOME . "pad/sequence/$pad_seq_dir/$pad_k.php" ) ) {

      pad_set_arr_var ( 'options_done', $pad_k, TRUE );

      $GLOBALS ["pad_seq_$pad_k"] = $pad_v;

      $GLOBALS ["pad_seq_$pad_seq_dir"] [] = $pad_k;

    }

?>