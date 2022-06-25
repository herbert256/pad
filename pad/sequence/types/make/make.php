<?php

  $pad_seq_make = [];

  foreach ( $pad_parms_tag as $pad_seq_make_name => $pad_seq_make_value )

    if ( $pad_seq_make_name <> 'make' and pad_file_exists ( PAD . "sequence/types/$pad_seq_make_name/make.php" ) ) {

      $pad_seq_make [] = $pad_seq_make_name;

      pad_set_arr_var ( 'options_done', $pad_seq_make_name, TRUE );

      $GLOBALS ["pad_seq_$pad_seq_make_name"] = $pad_seq_make_value;

    }

   $pad_seq_for = $pad_seq_store [$pad_seq_parm];

   
return include PAD . "sequence/type/for.php";

?>