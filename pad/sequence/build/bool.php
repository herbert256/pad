<?php
  
  $pad_seq_bool = [];

  foreach ( $pad_parms_tag as $pad_seq_bool_name => $pad_seq_bool_value )

    if ( $pad_seq_bool_name <> $pad_seq_seq and pad_file_exists ( PAD . "sequence/types/$pad_seq_bool_name/bool.php" ) ) {

      $pad_seq_bool [] = $pad_seq_bool_name;

      pad_set_arr_var ( 'options_done', $pad_seq_bool_name, TRUE );

      include_once PAD . "sequence/types/$pad_seq_bool_name/bool.php";

    }
  
?>